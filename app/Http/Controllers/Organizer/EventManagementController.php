<?php

namespace App\Http\Controllers\Organizer;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\EventCategory;
use App\Models\TicketType;
use App\Models\User;
use App\Services\EventCostCalculationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class EventManagementController extends Controller
{

    public function index(Request $request)
    {
        $organization = auth()->user()->currentOrganization();
        if (!$organization) {
            return redirect()->route('organizer.organizations.select');
        }

        $isArchive = $request->boolean('archive');

        $query = $organization->events()
            ->with(['category', 'bookings']);

        if ($isArchive) {
            // Archiv: Events, die bereits abgeschlossen sind (end_date in der Vergangenheit)
            $query->where('end_date', '<', now());
        } else {
            // Standard: aktuelle und zukünftige Events
            $query->where('end_date', '>=', now());
        }

        $events = $query->orderBy('start_date', $isArchive ? 'desc' : 'asc')->paginate(15);

        // Zähler für Tab-Badges
        $upcomingCount = $organization->events()->where('end_date', '>=', now())->count();
        $archiveCount  = $organization->events()->where('end_date', '<', now())->count();

        return view('organizer.events.index', compact('events', 'organization', 'isArchive', 'upcomingCount', 'archiveCount'));
    }

    public function create()
    {
        $organization = auth()->user()->currentOrganization();
        if (!$organization) {
            return redirect()->route('organizer.organizations.select');
        }
        $categories = EventCategory::where('is_active', true)->get();
        $event = new Event();
        $event->max_attendees = 50; // Default
        $event->price_from = 0;
        $event->is_featured = false;
        $costCalculationService = app(EventCostCalculationService::class);
        /** @var User $currentUser */
        $currentUser = User::findOrFail(auth()->id());
        $publishingCosts = $costCalculationService->calculatePublishingCosts($event, $currentUser);
        return view('organizer.events.create', compact('categories', 'publishingCosts', 'organization'));
    }

    public function store(Request $request)
    {
        $organization = auth()->user()->currentOrganization();
        if (!$organization) {
            return redirect()->route('organizer.organizations.select');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'event_category_id' => 'required|exists:event_categories,id',
            'event_type' => 'required|in:physical,online,hybrid,external',
            'description' => 'required|string',
            'start_date' => 'required|date|after:now',
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
            'external_booking_url' => 'required_if:event_type,external|nullable|url|max:2048',
            'external_booking_button_text' => 'nullable|string|max:100',
            'featured_image' => 'nullable|image|max:2048',
            'price_from' => 'nullable|numeric|min:0',
            'max_attendees' => 'nullable|integer|min:1',
            'is_published' => 'boolean',
            'is_featured' => 'boolean',
            'is_private' => 'boolean',
            'has_multiple_dates' => 'boolean',
            'access_code' => 'nullable|string|required_if:is_private,1',
            'ticket_notes' => 'nullable|string|max:1000',
            'show_qr_code_on_ticket' => 'boolean',
            'requires_ticket' => 'boolean',
            'cancellation_allowed' => 'boolean',
            'cancellation_days_before' => 'nullable|integer|min:0|required_if:cancellation_allowed,true',
            'organization_field_mode' => 'nullable|in:none,optional,required',
            'free_ticket_auto_confirm' => 'boolean',
            'tickets_before_invoice' => 'boolean',
        ]);

        if ($request->boolean('is_published')) {
            if (!$organization->hasCompleteBillingData()) {
                $errorMessage = 'Um Events zu veröffentlichen, müssen Sie zunächst die Rechnungsdaten und Bankverbindung Ihrer Organisation vervollständigen.';
                return back()->withErrors(['is_published' => $errorMessage])
                    ->with('error', $errorMessage)
                    ->with('redirect_to_settings', true)
                    ->withInput();
            }
        }

        $validated['organization_id'] = $organization->id;
        $validated['slug'] = Str::slug($validated['title']) . '-' . Str::random(6);

        // Handle Image Upload
        if ($request->hasFile('featured_image')) {
            $path = $request->file('featured_image')->store('events', 'public');
            $validated['featured_image'] = $path;
        }

        $event = Event::create($validated);

        // Create first EventDate if has_multiple_dates is enabled
        if ($request->boolean('has_multiple_dates')) {
            \App\Models\EventDate::create([
                'event_id' => $event->id,
                'start_date' => $event->start_date,
                'end_date' => $event->end_date,
                'venue_name' => $event->venue_name,
                'venue_address' => $event->venue_address,
                'venue_city' => $event->venue_city,
                'venue_postal_code' => $event->venue_postal_code,
                'venue_country' => $event->venue_country,
                'venue_latitude' => $event->venue_latitude,
                'venue_longitude' => $event->venue_longitude,
                'notes' => 'Erster Termin (aus Hauptevent übernommen)',
            ]);
        }

        // Create featured event booking if requested
        if ($request->boolean('is_featured') && $request->filled('featured_duration_type')) {
            $this->createFeaturedBooking($event, $request);
        }

        $successMessage = 'Event erfolgreich erstellt!';
        if ($request->boolean('has_multiple_dates')) {
            $successMessage .= ' Der erste Termin wurde automatisch angelegt.';
        }

        return redirect()->route('organizer.events.edit', $event)
            ->with('success', $successMessage)
            ->with('scroll_to_dates', $request->boolean('has_multiple_dates'));
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
        /** @var User $currentUser */
        $currentUser = User::findOrFail(auth()->id());

        try {
            $featuredFee = $featuredService->createFeaturedRequest(
                $event,
                $currentUser,
                $durationType,
                $startDate,
                $durationType === 'custom' ? (int)$customDays : null
            );

            // Store fee ID in session for redirect to payment
            session()->put('pending_featured_fee_id', $featuredFee->id);
        } catch (\Exception $e) {
            Log::error('Failed to create featured booking: ' . $e->getMessage());
        }
    }

    public function edit(Event $event)
    {
        $this->authorize('update', $event);

        // Load dates relation for multiple dates feature
        $event->load('dates');

        $categories = EventCategory::where('is_active', true)->get();
        $ticketTypes = $event->ticketTypes;

        // Calculate publishing costs
        $costCalculationService = app(EventCostCalculationService::class);
        /** @var User $currentUser */
        $currentUser = User::findOrFail(auth()->id());
        $publishingCosts = $costCalculationService->calculatePublishingCosts($event, $currentUser);

        return view('organizer.events.edit', compact('event', 'categories', 'ticketTypes', 'publishingCosts'));
    }

    public function update(Request $request, Event $event)
    {
        $this->authorize('update', $event);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'event_category_id' => 'required|exists:event_categories,id',
            'event_type' => 'required|in:physical,online,hybrid,external',
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
            'external_booking_url' => 'required_if:event_type,external|nullable|url|max:2048',
            'external_booking_button_text' => 'nullable|string|max:100',
            'featured_image' => 'nullable|image|max:2048',
            'price_from' => 'nullable|numeric|min:0',
            'max_attendees' => 'nullable|integer|min:1',
            'is_published' => 'boolean',
            'is_featured' => 'boolean',
            'is_private' => 'boolean',
            'access_code' => 'nullable|string|required_if:is_private,1',
            'ticket_notes' => 'nullable|string|max:1000',
            'show_qr_code_on_ticket' => 'boolean',
            'requires_ticket' => 'boolean',
            'cancellation_allowed' => 'boolean',
            'cancellation_days_before' => 'nullable|integer|min:0|required_if:cancellation_allowed,true',
            'organization_field_mode' => 'nullable|in:none,optional,required',
            'free_ticket_auto_confirm' => 'boolean',
            'tickets_before_invoice' => 'boolean',
        ]);

        unset($validated['has_multiple_dates']);

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


        // Handle Image Upload / Deletion
        if ($request->hasFile('featured_image')) {
            // Altes Bild löschen wenn vorhanden
            if ($event->featured_image) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($event->featured_image);
            }
            $path = $request->file('featured_image')->store('events', 'public');
            $validated['featured_image'] = $path;
        } elseif ($request->boolean('delete_featured_image')) {
            // Vorhandenes Bild entfernen
            if ($event->featured_image) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($event->featured_image);
            }
            $validated['featured_image'] = null;
        } else {
            // Kein neues Bild – vorhandenes Bild NICHT überschreiben
            unset($validated['featured_image']);
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

        // Preisänderung verhindern, wenn aktive (nicht-stornierte) Buchungen existieren
        if ($ticketType->hasActiveSales() && (float)$validated['price'] !== (float)$ticketType->price) {
            return back()->withErrors(['price' => 'Der Preis kann nicht geändert werden, da noch aktive Buchungen für diesen Ticket-Typ vorhanden sind.']);
        }

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

        if ($event->is_cancelled) {
            return back()->with('error', 'Diese Veranstaltung wurde bereits abgesagt.');
        }

        $event->update([
            'is_cancelled' => true,
            'cancelled_at' => now(),
            'cancellation_reason' => $validated['cancellation_reason'] ?? null,
        ]);

        // Alle aktiven Buchungen (auch unbezahlte und nicht freigegebene) stornieren
        // und jeden Kunden genau einmal per E-Mail informieren.
        $count = app(\App\Services\BookingWorkflowService::class)->cancelAllForEvent($event);

        return redirect()->route('organizer.events.index')
            ->with('success', "Die Veranstaltung wurde abgesagt. {$count} Buchung(en) wurden storniert und per E-Mail informiert. "
                . 'Bitte veranlassen Sie ggf. Erstattungen bereits bezahlter Buchungen.');
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
     * Zielgruppen für Nachrichten an Teilnehmende.
     */
    public const MESSAGE_SEGMENTS = [
        'all' => 'Alle aktiven Buchungen',
        'ready' => 'Bestätigte Teilnehmende (Tickets/Zugang freigegeben)',
        'unpaid' => 'Buchungen mit offener Zahlung/Rechnung',
        'pending_approval' => 'Anmeldungen, die auf Freigabe warten',
        'checked_in' => 'Eingecheckte Teilnehmende',
        'not_checked_in' => 'Bestätigt, aber nicht eingecheckt',
    ];

    protected function segmentQuery(Event $event, string $segment)
    {
        $query = \App\Models\Booking::where('event_id', $event->id);

        return match ($segment) {
            'ready' => $query->readyForParticipation(),
            'unpaid' => $query->whereIn('status', ['pending', 'confirmed'])
                ->where('total', '>', 0)
                ->whereNotIn('payment_status', ['paid', 'extern']),
            'pending_approval' => $query->where('status', 'pending_approval'),
            'checked_in' => $query->readyForParticipation()->whereHas('items', fn ($q) => $q->where('checked_in', true)),
            'not_checked_in' => $query->readyForParticipation()->whereHas('items', fn ($q) => $q->where('checked_in', false)),
            default => $query->whereIn('status', ['pending', 'pending_approval', 'confirmed', 'completed']),
        };
    }

    /**
     * Show form to contact attendees
     */
    public function contactAttendeesForm(Event $event)
    {
        $this->authorize('view', $event);

        $segments = collect(self::MESSAGE_SEGMENTS)
            ->map(fn ($label, $key) => ['label' => $label, 'count' => $this->segmentQuery($event, $key)->count()]);
        $attendeesCount = $segments['all']['count'];

        return view('organizer.events.contact-attendees', compact('event', 'attendeesCount', 'segments'));
    }

    /**
     * Nachricht an eine Zielgruppe senden (über die Queue, protokolliert im E-Mail-Verlauf jeder Buchung).
     */
    public function contactAttendees(Request $request, Event $event)
    {
        $this->authorize('view', $event);

        $validated = $request->validate([
            'segment' => 'nullable|in:' . implode(',', array_keys(self::MESSAGE_SEGMENTS)),
            'include_attendees' => 'nullable|boolean',
            'subject' => 'required|string|max:255',
            'message' => 'required|string|max:5000',
        ]);

        $segment = $validated['segment'] ?? 'all';
        $bookings = $this->segmentQuery($event, $segment)->with(['items', 'event.organization'])->get();

        $recipients = 0;
        foreach ($bookings as $booking) {
            \Illuminate\Support\Facades\Mail::to($booking->customer_email)->queue(
                new \App\Mail\AttendeeMessageMail($booking, $validated['subject'], $validated['message'])
            );
            $recipients++;

            if ($request->boolean('include_attendees')) {
                $booking->items
                    ->filter(fn ($item) => $item->separateAttendeeEmail())
                    ->unique(fn ($item) => mb_strtolower($item->separateAttendeeEmail()))
                    ->each(function ($item) use ($booking, $validated, &$recipients) {
                        \Illuminate\Support\Facades\Mail::to($item->separateAttendeeEmail())->queue(
                            new \App\Mail\AttendeeMessageMail($booking, $validated['subject'], $validated['message'], $item->participantName())
                        );
                        $recipients++;
                    });
            }
        }

        return redirect()->route('organizer.events.attendees.contact', $event)
            ->with('success', "Die Nachricht wird an {$recipients} Empfänger:innen versendet ({$bookings->count()} Buchungen). "
                . 'Sie erscheint im E-Mail-Verlauf der jeweiligen Buchung.');
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
        /** @var User $currentUser */
        $currentUser = User::findOrFail(auth()->id());
        $publishingCosts = $costCalculationService->calculatePublishingCosts($event, $currentUser);

        return response()->json([
            'success' => true,
            'costs' => $publishingCosts,
        ]);
    }
}
