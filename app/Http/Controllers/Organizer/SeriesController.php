<?php

namespace App\Http\Controllers\Organizer;

use App\Http\Controllers\Controller;
use App\Models\EventSeries;
use App\Models\EventCategory;
use App\Models\Event;
use App\Services\EventCostCalculationService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class SeriesController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth']);
    }

    /**
     * Display a listing of event series
     */
    public function index()
    {
        $organization = auth()->user()->currentOrganization();
        if (!$organization) {
            return redirect()->route('organizer.organizations.select');
        }

        $series = EventSeries::where('organization_id', $organization->id)
            ->with(['category', 'events'])
            ->withCount('events')
            ->latest()
            ->paginate(15);

        return view('organizer.series.index', compact('series', 'organization'));
    }

    /**
     * Show the form for creating a new series
     */
    public function create()
    {
        $organization = auth()->user()->currentOrganization();
        if (!$organization) {
            return redirect()->route('organizer.organizations.select');
        }

        $categories = EventCategory::where('is_active', true)->get();
        // Create empty event for cost estimation
        $event = new Event();
        $event->max_attendees = 50; // Default
        $event->price_from = 0;
        $event->is_featured = false;

        // Calculate initial costs (will be multiplied by event count)
        $costCalculationService = app(EventCostCalculationService::class);
        $publishingCosts = $costCalculationService->calculatePublishingCosts($event, auth()->user());

        // Get platform fee info for display when no specific costs available
        $platformFeeInfo = $costCalculationService->getPlatformFeeInfo(auth()->user());

        return view('organizer.series.create', compact('categories', 'publishingCosts', 'platformFeeInfo', 'organization'));
    }

    /**
     * Store a newly created series
     */
    public function store(Request $request)
    {
        $organization = auth()->user()->currentOrganization();
        if (!$organization) {
            return redirect()->route('organizer.organizations.select');
        }

        try {
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'event_category_id' => 'required|exists:event_categories,id',

                // Simplified recurrence - nur Anzahl der Termine
                'recurrence_count' => 'required|integer|min:1|max:50',

                // Template data
                'start_time' => 'required|date_format:H:i',
                'duration' => 'required|integer|min:15|max:1440',

                // Venue
                'venue_name' => 'required|string|max:255',
                'venue_address' => 'required|string',
                'venue_city' => 'required|string|max:255',
                'venue_postal_code' => 'required|string|max:20',
                'venue_country' => 'required|string|max:100',

                // Optional
                'organizer_info' => 'nullable|string',
                'organizer_email' => 'nullable|email',
                'organizer_phone' => 'nullable|string',
                'max_attendees' => 'nullable|integer|min:1',
                'is_published' => 'boolean',
            ]);

            // Create series with simplified data
            $series = EventSeries::create([
                'organization_id' => $organization->id,
                'title' => $validated['title'],
                'description' => $validated['description'] ?? null,
                'event_category_id' => $validated['event_category_id'],
                'recurrence_type' => 'manual', // Simplified: manual entry
                'recurrence_interval' => 1,
                'recurrence_count' => $validated['recurrence_count'],
                'template_data' => [
                    'start_time' => $validated['start_time'],
                    'duration' => (int) $validated['duration'],
                    'venue_name' => $validated['venue_name'],
                    'venue_address' => $validated['venue_address'],
                    'venue_city' => $validated['venue_city'],
                    'venue_postal_code' => $validated['venue_postal_code'],
                    'venue_country' => $validated['venue_country'],
                    'organizer_info' => $validated['organizer_info'] ?? null,
                    'organizer_email' => $validated['organizer_email'] ?? null,
                    'organizer_phone' => $validated['organizer_phone'] ?? null,
                    'max_attendees' => $validated['max_attendees'] ?? null,
                    'is_published' => $validated['is_published'] ?? false,
                ],
                'is_active' => true,
            ]);

            // Generate placeholder events
            $this->generatePlaceholderEvents($series);

            return redirect()->route('organizer.series.show', $series)
                ->with('success', 'Veranstaltungsreihe erstellt!');
        } catch (\Exception $e) {
            \Log::error('Series creation error: ' . $e->getMessage());
            return back()->withInput()->withErrors(['error' => 'Fehler beim Erstellen der Veranstaltungsreihe: ' . $e->getMessage()]);
        }
    }

    /**
     * Display the specified series
     */
    public function show(EventSeries $series)
    {
        $this->authorize('view', $series);

        $series->load(['events', 'category']);

        return view('organizer.series.show', compact('series'));
    }

    /**
     * Show the form for editing the specified series
     */
    public function edit(EventSeries $series)
    {
        $this->authorize('update', $series);

        $categories = EventCategory::where('is_active', true)->get();

        // Create sample event from series template for cost estimation
        $event = new Event();
        $templateData = $series->template_data ?? [];
        $event->max_attendees = $templateData['max_attendees'] ?? 50;
        $event->price_from = $templateData['price_from'] ?? 0;
        $event->is_featured = $templateData['is_featured'] ?? false;

        // Calculate costs per event
        $costCalculationService = app(EventCostCalculationService::class);
        $publishingCosts = $costCalculationService->calculatePublishingCosts($event, auth()->user());

        // Get platform fee info for display when no specific costs available
        $platformFeeInfo = $costCalculationService->getPlatformFeeInfo(auth()->user());

        return view('organizer.series.edit', compact('series', 'categories', 'publishingCosts', 'platformFeeInfo'));
    }

    /**
     * Update the specified series
     */
    public function update(Request $request, EventSeries $series)
    {
        $this->authorize('update', $series);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $series->update($validated);

        return redirect()->route('organizer.series.show', $series)
            ->with('success', 'Veranstaltungsreihe erfolgreich aktualisiert!');
    }

    /**
     * Remove the specified series
     */
    public function destroy(EventSeries $series)
    {
        $this->authorize('delete', $series);

        // Check if there are bookings
        $hasBookings = $series->events()->whereHas('bookings')->exists();

        if ($hasBookings) {
            return back()->with('error', 'Veranstaltungsreihe kann nicht gelöscht werden, da bereits Buchungen vorhanden sind.');
        }

        // Delete all events in series
        $series->events()->delete();
        $series->delete();

        return redirect()->route('organizer.series.index')
            ->with('success', 'Veranstaltungsreihe und alle zugehörigen Events gelöscht!');
    }

    /**
     * Generate placeholder events for series
     * Creates simple numbered events that organizers can customize
     */
    protected function generatePlaceholderEvents(EventSeries $series)
    {
        $template = $series->template_data;
        $count = $series->recurrence_count ?? 5;

        // Start from tomorrow by default
        $baseDate = now()->addDay();

        for ($i = 1; $i <= $count; $i++) {
            // Create placeholder date (can be changed by organizer later)
            $startDate = $baseDate->copy()->addDays($i - 1)->setTimeFromTimeString($template['start_time']);
            $endDate = $startDate->copy()->addMinutes($template['duration']);

            Event::create([
                'organization_id' => $series->organization_id,
                'series_id' => $series->id,
                'series_position' => $i,
                'is_series_part' => true, // WICHTIG: Einzelbuchung nicht möglich
                'event_category_id' => $series->event_category_id,
                'title' => $series->title . ' - Termin ' . $i,
                'slug' => Str::slug($series->title . '-termin-' . $i) . '-' . Str::random(6),
                'description' => $series->description ?? 'Termin ' . $i . ' der Veranstaltungsreihe',
                'start_date' => $startDate,
                'end_date' => $endDate,
                'venue_name' => $template['venue_name'],
                'venue_address' => $template['venue_address'],
                'venue_city' => $template['venue_city'],
                'venue_postal_code' => $template['venue_postal_code'],
                'venue_country' => $template['venue_country'],
                'organizer_info' => $template['organizer_info'] ?? null,
                'organizer_email' => $template['organizer_email'] ?? null,
                'organizer_phone' => $template['organizer_phone'] ?? null,
                'max_attendees' => $template['max_attendees'] ?? null,
                'is_published' => false, // Termine werden NICHT veröffentlicht
                'is_featured' => false,
                'registration_required' => false, // Keine Einzelregistrierung
            ]);
        }

        // Update total count
        $series->update(['total_events' => $count]);
    }

    /**
     * Regenerate events for series
     */
    public function regenerate(EventSeries $series)
    {
        $this->authorize('update', $series);

        // Delete events ohne Buchungen
        $series->events()->whereDoesntHave('bookings')->delete();

        return redirect()->route('organizer.series.show', $series)
            ->with('success', 'Events ohne Buchungen wurden gelöscht.');
    }

    /**
     * Add single event to series
     */
    public function addEvent(Request $request, EventSeries $series)
    {
        $this->authorize('update', $series);

        $validated = $request->validate([
            'event_date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
        ]);

        $template = $series->template_data;
        $startDateTime = Carbon::parse($validated['event_date'] . ' ' . $validated['start_time']);
        $endDateTime = $startDateTime->copy()->addMinutes($template['duration']);

        $position = (int) $series->events()->max('series_position') + 1;

        Event::create([
            'organization_id' => $series->organization_id,
            'series_id' => $series->id,
            'series_position' => $position,
            'event_category_id' => $series->event_category_id,
            'title' => $series->title . ' - Teil ' . $position,
            'slug' => Str::slug($series->title . '-teil-' . $position) . '-' . Str::random(6),
            'description' => $series->description,
            'start_date' => $startDateTime,
            'end_date' => $endDateTime,
            'venue_name' => $template['venue_name'],
            'venue_address' => $template['venue_address'],
            'venue_city' => $template['venue_city'],
            'venue_postal_code' => $template['venue_postal_code'],
            'venue_country' => $template['venue_country'],
        ]);

        return redirect()->route('organizer.series.show', $series)
            ->with('success', 'Event zur Reihe hinzugefügt.');
    }
}

