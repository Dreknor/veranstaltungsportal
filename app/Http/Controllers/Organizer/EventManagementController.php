<?php

namespace App\Http\Controllers\Organizer;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\EventCategory;
use App\Models\TicketType;
use App\Services\EventCostCalculationService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class EventManagementController extends Controller
{

    public function index()
    {
        $events = Event::where('user_id', auth()->id())
            ->with(['category', 'bookings'])
            ->latest()
            ->paginate(15);

        return view('organizer.events.index', compact('events'));
    }

    public function create()
    {
        $categories = EventCategory::where('is_active', true)->get();

        // Create empty event for cost estimation
        $event = new Event();
        $event->max_attendees = 50; // Default
        $event->price_from = 0;
        $event->is_featured = false;

        // Calculate initial costs
        $costCalculationService = app(EventCostCalculationService::class);
        $publishingCosts = $costCalculationService->calculatePublishingCosts($event, auth()->user());

        return view('organizer.events.create', compact('categories', 'publishingCosts'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'event_category_id' => 'required|exists:event_categories,id',
            'event_type' => 'required|in:physical,online,hybrid',
            'description' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'venue_name' => 'required_if:event_type,physical,hybrid|nullable|string|max:255',
            'venue_address' => 'required_if:event_type,physical,hybrid|nullable|string',
            'venue_city' => 'required_if:event_type,physical,hybrid|nullable|string|max:255',
            'venue_postal_code' => 'required_if:event_type,physical,hybrid|nullable|string|max:20',
            'venue_country' => 'required_if:event_type,physical,hybrid|nullable|string|max:100',
            'venue_latitude' => 'nullable|numeric',
            'venue_longitude' => 'nullable|numeric',
            'directions' => 'nullable|string',
            'online_url' => 'required_if:event_type,online,hybrid|nullable|url',
            'online_access_code' => 'nullable|string|max:255',
            'featured_image' => 'nullable|image|max:2048',
            'video_url' => 'nullable|url',
            'livestream_url' => 'nullable|url',
            'price_from' => 'nullable|numeric|min:0',
            'max_attendees' => 'nullable|integer|min:1',
            'is_published' => 'boolean',
            'is_featured' => 'boolean',
            'is_private' => 'boolean',
            'access_code' => 'nullable|string|required_if:is_private,1',
            'organizer_info' => 'nullable|string',
            'organizer_email' => 'nullable|email',
            'organizer_phone' => 'nullable|string',
            'organizer_website' => 'nullable|url',
        ]);

        // Check if trying to publish without complete organizer data
        if ($request->boolean('is_published')) {
            $user = auth()->user();

            if (!$user->canPublishEvents()) {
                $missingData = $user->getMissingOrganizerData();
                $errorMessage = 'Um Events zu veröffentlichen, müssen Sie zunächst Ihre ';

                if (in_array('billing_data', $missingData) && in_array('bank_account', $missingData)) {
                    $errorMessage .= 'Rechnungsdaten und Bankverbindung';
                } elseif (in_array('billing_data', $missingData)) {
                    $errorMessage .= 'Rechnungsdaten';
                } else {
                    $errorMessage .= 'Bankverbindung';
                }

                $errorMessage .= ' vervollständigen. Diese Angaben sind notwendig, da bei Buchungen automatisch Rechnungen versendet werden.';

                return back()->withErrors(['is_published' => $errorMessage])
                    ->with('error', $errorMessage)
                    ->with('redirect_to_settings', true)
                    ->withInput();
            }
        }

        $validated['user_id'] = auth()->id();
        $validated['slug'] = Str::slug($validated['title']) . '-' . Str::random(6);

        // Handle Image Upload
        if ($request->hasFile('featured_image')) {
            $path = $request->file('featured_image')->store('events', 'public');
            $validated['featured_image'] = $path;
        }

        $event = Event::create($validated);

        // Create featured event booking if requested
        if ($request->boolean('is_featured') && $request->filled('featured_duration_type')) {
            $this->createFeaturedBooking($event, $request);
        }

        return redirect()->route('organizer.events.edit', $event)
            ->with('success', 'Event erfolgreich erstellt!');
    }

    /**
     * Create featured event booking
     */
    private function createFeaturedBooking(Event $event, Request $request)
    {
        $durationType = $request->input('featured_duration_type');
        $customDays = $request->input('featured_custom_days');
        $startDate = \Carbon\Carbon::parse($request->input('featured_start_date', now()));

        $featuredService = app(\App\Services\FeaturedEventService::class);

        try {
            $featuredFee = $featuredService->createFeaturedRequest(
                $event,
                auth()->user(),
                $durationType,
                $startDate,
                $durationType === 'custom' ? (int)$customDays : null
            );

            // Store fee ID in session for redirect to payment
            session()->put('pending_featured_fee_id', $featuredFee->id);
        } catch (\Exception $e) {
            \Log::error('Failed to create featured booking: ' . $e->getMessage());
        }
    }

    public function edit(Event $event)
    {
        $this->authorize('update', $event);

        $categories = EventCategory::where('is_active', true)->get();
        $ticketTypes = $event->ticketTypes;

        // Calculate publishing costs
        $costCalculationService = app(EventCostCalculationService::class);
        $publishingCosts = $costCalculationService->calculatePublishingCosts($event, auth()->user());

        return view('organizer.events.edit', compact('event', 'categories', 'ticketTypes', 'publishingCosts'));
    }

    public function update(Request $request, Event $event)
    {
        $this->authorize('update', $event);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'event_category_id' => 'required|exists:event_categories,id',
            'event_type' => 'required|in:physical,online,hybrid',
            'description' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'venue_name' => 'required_if:event_type,physical,hybrid|nullable|string|max:255',
            'venue_address' => 'required_if:event_type,physical,hybrid|nullable|string',
            'venue_city' => 'required_if:event_type,physical,hybrid|nullable|string|max:255',
            'venue_postal_code' => 'required_if:event_type,physical,hybrid|nullable|string|max:20',
            'venue_country' => 'required_if:event_type,physical,hybrid|nullable|string|max:100',
            'venue_latitude' => 'nullable|numeric',
            'venue_longitude' => 'nullable|numeric',
            'directions' => 'nullable|string',
            'online_url' => 'required_if:event_type,online,hybrid|nullable|url',
            'online_access_code' => 'nullable|string|max:255',
            'featured_image' => 'nullable|image|max:2048',
            'video_url' => 'nullable|url',
            'livestream_url' => 'nullable|url',
            'price_from' => 'nullable|numeric|min:0',
            'max_attendees' => 'nullable|integer|min:1',
            'is_published' => 'boolean',
            'is_featured' => 'boolean',
            'is_private' => 'boolean',
            'access_code' => 'nullable|string|required_if:is_private,1',
            'organizer_info' => 'nullable|string',
            'organizer_email' => 'nullable|email',
            'organizer_phone' => 'nullable|string',
            'organizer_website' => 'nullable|url',
        ]);

        // Check if trying to publish without complete organizer data
        if ($request->boolean('is_published') && !$event->is_published) {
            $user = auth()->user();

            if (!$user->canPublishEvents()) {
                $missingData = $user->getMissingOrganizerData();
                $errorMessage = 'Um Events zu veröffentlichen, müssen Sie zunächst Ihre ';

                if (in_array('billing_data', $missingData) && in_array('bank_account', $missingData)) {
                    $errorMessage .= 'Rechnungsdaten und Bankverbindung';
                } elseif (in_array('billing_data', $missingData)) {
                    $errorMessage .= 'Rechnungsdaten';
                } else {
                    $errorMessage .= 'Bankverbindung';
                }

                $errorMessage .= ' vervollständigen. Diese Angaben sind notwendig, da bei Buchungen automatisch Rechnungen versendet werden.';

                return back()->withErrors(['is_published' => $errorMessage])
                    ->with('error', $errorMessage)
                    ->with('redirect_to_settings', true);
            }
        }

        // Handle Image Upload
        if ($request->hasFile('featured_image')) {
            $path = $request->file('featured_image')->store('events', 'public');
            $validated['featured_image'] = $path;
        }

        $event->update($validated);

        // Create featured event booking if newly requested
        $wasFeatured = $event->getOriginal('is_featured');
        $isFeaturedNow = $request->boolean('is_featured');

        if ($isFeaturedNow && !$wasFeatured && $request->filled('featured_duration_type')) {
            $this->createFeaturedBooking($event, $request);
        }

        return redirect()->route('organizer.events.edit', $event)
            ->with('success', 'Event erfolgreich aktualisiert!');
    }

    public function destroy(Event $event)
    {
        $this->authorize('delete', $event);

        $event->delete();

        return redirect()->route('organizer.events.index')
            ->with('success', 'Event erfolgreich gelöscht!');
    }

    public function duplicate(Event $event)
    {
        $this->authorize('view', $event);

        // Dupliziere Event
        $newEvent = $event->replicate();
        $newEvent->title = $event->title . ' (Kopie)';
        $newEvent->slug = Str::slug($newEvent->title) . '-' . Str::random(6);
        $newEvent->is_published = false;
        $newEvent->is_featured = false;
        $newEvent->created_at = now();
        $newEvent->updated_at = now();
        $newEvent->save();

        // Dupliziere Ticket-Typen
        foreach ($event->ticketTypes as $ticketType) {
            $newTicketType = $ticketType->replicate();
            $newTicketType->event_id = $newEvent->id;
            $newTicketType->quantity_sold = 0;
            $newTicketType->save();
        }

        // Dupliziere Rabattcodes
        foreach ($event->discountCodes as $discountCode) {
            $newDiscountCode = $discountCode->replicate();
            $newDiscountCode->event_id = $newEvent->id;
            $newDiscountCode->usage_count = 0;
            $newDiscountCode->save();
        }

        return redirect()->route('organizer.events.edit', $newEvent)
            ->with('success', 'Event erfolgreich dupliziert! Bitte aktualisieren Sie die Daten.');
    }

    public function addTicketType(Request $request, Event $event)
    {
        $this->authorize('update', $event);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'quantity' => 'nullable|integer|min:1',
            'sale_start' => 'nullable|date',
            'sale_end' => 'nullable|date|after:sale_start',
            'min_per_order' => 'required|integer|min:1',
            'max_per_order' => 'nullable|integer|min:1',
            'is_available' => 'boolean',
        ]);

        $validated['event_id'] = $event->id;
        TicketType::create($validated);

        return back()->with('success', 'Ticket-Typ erfolgreich hinzugefügt!');
    }

    public function updateTicketType(Request $request, Event $event, TicketType $ticketType)
    {
        $this->authorize('update', $event);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'quantity' => 'nullable|integer|min:1',
            'sale_start' => 'nullable|date',
            'sale_end' => 'nullable|date|after:sale_start',
            'min_per_order' => 'required|integer|min:1',
            'max_per_order' => 'nullable|integer|min:1',
            'is_available' => 'boolean',
        ]);

        $ticketType->update($validated);

        return back()->with('success', 'Ticket-Typ erfolgreich aktualisiert!');
    }

    public function deleteTicketType(Event $event, TicketType $ticketType)
    {
        $this->authorize('update', $event);

        if ($ticketType->quantity_sold > 0) {
            return back()->with('error', 'Ticket-Typ kann nicht gelöscht werden, da bereits Tickets verkauft wurden.');
        }

        $ticketType->delete();

        return back()->with('success', 'Ticket-Typ erfolgreich gelöscht!');
    }

    /**
     * Cancel an event
     */
    public function cancel(Request $request, Event $event)
    {
        $this->authorize('update', $event);

        $validated = $request->validate([
            'cancellation_reason' => 'nullable|string|max:1000',
        ]);

        $event->update([
            'is_cancelled' => true,
            'cancelled_at' => now(),
            'cancellation_reason' => $validated['cancellation_reason'] ?? null,
        ]);

        // Notify all attendees about the cancellation
        $attendees = $event->getAttendees();

        foreach ($attendees as $booking) {
            // Send dedicated email
            \Illuminate\Support\Facades\Mail::to($booking->customer_email)
                ->send(new \App\Mail\EventCancelledMail($event, $booking));

            // Also send notification for in-app notifications
            if ($booking->user) {
                $booking->user->notify(new \App\Notifications\EventCancelledNotification($event, $booking));
            }

            // Update booking status to cancelled
            $booking->update([
                'status' => 'cancelled',
                'cancelled_at' => now(),
            ]);
        }

        return redirect()->route('organizer.events.index')
            ->with('success', 'Event wurde abgesagt und alle ' . $attendees->count() . ' Teilnehmer wurden per E-Mail benachrichtigt.');
    }

    /**
     * Download attendees list as CSV
     */
    public function downloadAttendees(Event $event)
    {
        $this->authorize('view', $event);

        $attendees = $event->getAttendees();

        $filename = 'teilnehmerliste-' . Str::slug($event->title) . '-' . date('Y-m-d') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($attendees) {
            $file = fopen('php://output', 'w');

            // Add UTF-8 BOM for proper Excel encoding
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

            // Header row
            fputcsv($file, [
                'Buchungsnummer',
                'Name',
                'E-Mail',
                'Telefon',
                'Anzahl Tickets',
                'Betrag',
                'Status',
                'Buchungsdatum',
            ], ';');

            // Data rows
            foreach ($attendees as $booking) {
                fputcsv($file, [
                    $booking->booking_number,
                    $booking->customer_name,
                    $booking->customer_email,
                    $booking->customer_phone ?? '-',
                    $booking->items->sum('quantity'),
                    number_format($booking->total, 2, ',', '.') . ' €',
                    $booking->status,
                    $booking->created_at->format('d.m.Y H:i'),
                ], ';');
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Show form to contact attendees
     */
    public function contactAttendeesForm(Event $event)
    {
        $this->authorize('view', $event);

        $attendeesCount = $event->getAttendeesCount();

        return view('organizer.events.contact-attendees', compact('event', 'attendeesCount'));
    }

    /**
     * Send message to all attendees
     */
    public function contactAttendees(Request $request, Event $event)
    {
        $this->authorize('view', $event);

        $validated = $request->validate([
            'subject' => 'required|string|max:255',
            'message' => 'required|string|max:5000',
        ]);

        $attendees = $event->getAttendees();

        foreach ($attendees as $booking) {
            $email = $booking->customer_email;

            \Illuminate\Support\Facades\Mail::send([], [], function ($message) use ($email, $validated, $event) {
                $message->to($email)
                    ->subject($validated['subject'])
                    ->html(nl2br(e($validated['message'])) . '<br><br>---<br>Diese Nachricht bezieht sich auf die Veranstaltung: ' . $event->title . '<br>Datum: ' . $event->start_date->format('d.m.Y H:i') . ' Uhr');
            });
        }

        return redirect()->route('organizer.events.edit', $event)
            ->with('success', 'Nachricht wurde an ' . $attendees->count() . ' Teilnehmer gesendet.');
    }

    /**
     * Calculate costs for event publishing (AJAX)
     */
    public function calculateCosts(Request $request, Event $event)
    {
        $this->authorize('update', $event);

        // Temporarily update event with form data (without saving)
        $event->is_featured = $request->boolean('is_featured');
        $event->max_attendees = $request->input('max_attendees', $event->max_attendees);
        $event->price_from = $request->input('price_from', $event->price_from);

        // Calculate costs
        $costCalculationService = app(EventCostCalculationService::class);
        $publishingCosts = $costCalculationService->calculatePublishingCosts($event, auth()->user());

        return response()->json([
            'success' => true,
            'costs' => $publishingCosts,
        ]);
    }
}
