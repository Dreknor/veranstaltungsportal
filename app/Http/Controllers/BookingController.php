<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\BookingItem;
use App\Models\DiscountCode;
use App\Models\Event;
use App\Models\TicketType;
use App\Services\InvoiceService;
use App\Services\TicketPdfService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class BookingController extends Controller
{
    public function create(Event $event)
    {
        // Check if event is cancelled
        if ($event->is_cancelled) {
            return redirect()->route('events.show', $event->slug)
                ->with('error', 'Diese Veranstaltung wurde abgesagt und kann nicht mehr gebucht werden.');
        }

        // Get available ticket types
        $ticketTypes = $event->ticketTypes()
            ->where('is_available', true)
            ->get()
            ->filter(function ($ticket) {
                return $ticket->isOnSale() && $ticket->availableQuantity() > 0;
            });

        // If price_from is set and there are no ticket types, create a default one
        if ($ticketTypes->isEmpty() && $event->price_from !== null) {
            $price = $event->price_from;

            $defaultTicket = TicketType::create([
                'event_id' => $event->id,
                'name' => $price > 0 ? 'Standard-Ticket' : 'Kostenlose Teilnahme',
                'description' => 'Regulärer Zugang zur Veranstaltung',
                'price' => $price,
                'quantity' => $event->max_attendees ?? null,
                'quantity_sold' => 0,
                'is_available' => true,
                'min_per_order' => 1,
                'max_per_order' => $event->max_attendees ? min(10, $event->max_attendees) : 10,
                'sale_start' => null,
                'sale_end' => null,
            ]);

            // Add to collection
            $ticketTypes = collect([$defaultTicket]);
        }

        if ($ticketTypes->isEmpty()) {
            // Check why no tickets are available
            $allTickets = $event->ticketTypes;
            $reasons = [];

            foreach ($allTickets as $ticket) {
                if (!$ticket->is_available) {
                    $reasons[] = "{$ticket->name}: Nicht verfügbar";
                } elseif (!$ticket->isOnSale()) {
                    if ($ticket->sale_start && now()->lt($ticket->sale_start)) {
                        $reasons[] = "{$ticket->name}: Verkauf beginnt am {$ticket->sale_start->format('d.m.Y')}";
                    } elseif ($ticket->sale_end && now()->gt($ticket->sale_end)) {
                        $reasons[] = "{$ticket->name}: Verkauf endete am {$ticket->sale_end->format('d.m.Y')}";
                    }
                } elseif ($ticket->availableQuantity() === 0) {
                    $reasons[] = "{$ticket->name}: Ausverkauft";
                }
            }

            $message = 'Keine Tickets verfügbar.';
            if (!empty($reasons)) {
                $message .= ' Gründe: ' . implode(' | ', $reasons);
            }

            return redirect()->route('events.show', $event->slug)
                ->with('error', $message);
        }

        return view('bookings.create', compact('event', 'ticketTypes'));
    }

    public function store(Request $request, Event $event)
    {
        // Check if event is cancelled
        if ($event->is_cancelled) {
            return redirect()->route('events.show', $event->slug)
                ->with('error', 'Diese Veranstaltung wurde abgesagt und kann nicht mehr gebucht werden.');
        }

        // Load organization to check PayPal availability
        $event->load('organization');

        $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'required|email|max:255',
            'customer_phone' => 'nullable|string|max:50',
            'billing_address' => 'required|string|max:255',
            'billing_postal_code' => 'required|string|max:20',
            'billing_city' => 'required|string|max:255',
            'billing_country' => 'required|string|max:100',
            'tickets' => 'required|array|min:1',
            'tickets.*.ticket_type_id' => 'required|exists:ticket_types,id',
            'tickets.*.quantity' => 'required|integer|min:0',
            'discount_code' => 'nullable|string',
            'payment_method' => 'nullable|string|in:invoice,paypal',
        ]);

        // Validate PayPal selection
        if ($request->payment_method === 'paypal' && !$event->organization->hasPayPalConfigured()) {
            return back()->withInput()
                ->with('error', 'PayPal ist für diesen Veranstalter nicht verfügbar.');
        }

        try {
            return DB::transaction(function () use ($request, $event) {
                $subtotal = 0;
                $ticketData = [];

                // Log die eingehenden Ticket-Daten für Debugging
                Log::info('Booking store - Incoming tickets data', [
                    'tickets' => $request->tickets,
                    'event_id' => $event->id,
                ]);

                // Validiere und berechne Tickets
                foreach ($request->tickets as $index => $ticketInput) {
                    // Überspringe wenn ticketInput null ist
                    if ($ticketInput === null || !is_array($ticketInput)) {
                        Log::warning('Skipping invalid ticket input', ['index' => $index, 'value' => $ticketInput]);
                        continue;
                    }

                    // Überspringe wenn quantity nicht gesetzt ist oder 0 ist
                    if (!isset($ticketInput['quantity']) || $ticketInput['quantity'] < 1) {
                        Log::debug('Skipping ticket with zero quantity', ['index' => $index, 'ticket' => $ticketInput]);
                        continue;
                    }

                    // Überspringe wenn ticket_type_id nicht gesetzt ist oder null ist
                    if (!isset($ticketInput['ticket_type_id']) || $ticketInput['ticket_type_id'] === null) {
                        Log::warning('Skipping ticket with null ticket_type_id', ['index' => $index, 'ticket' => $ticketInput]);
                        continue;
                    }

                    try {
                        $ticketType = TicketType::findOrFail($ticketInput['ticket_type_id']);
                    } catch (\Exception $e) {
                        Log::error('Ticket type not found', [
                            'ticket_type_id' => $ticketInput['ticket_type_id'],
                            'error' => $e->getMessage()
                        ]);
                        throw new \Exception("Ungültiger Ticket-Typ ausgewählt.");
                    }

                    // Prüfe Verfügbarkeit
                    if ($ticketType->availableQuantity() < $ticketInput['quantity']) {
                        throw new \Exception("Nicht genügend Tickets für {$ticketType->name} verfügbar.");
                    }

                    // Prüfe Min/Max
                    if ($ticketInput['quantity'] < $ticketType->min_per_order) {
                        throw new \Exception("Mindestbestellmenge für {$ticketType->name}: {$ticketType->min_per_order}");
                    }

                    if ($ticketType->max_per_order && $ticketInput['quantity'] > $ticketType->max_per_order) {
                        throw new \Exception("Maximalbestellmenge für {$ticketType->name}: {$ticketType->max_per_order}");
                    }

                    $itemTotal = $ticketType->price * $ticketInput['quantity'];
                    $subtotal += $itemTotal;

                    $ticketData[] = [
                        'ticket_type' => $ticketType,
                        'quantity' => $ticketInput['quantity'],
                        'price' => $ticketType->price,
                    ];
                }

                // Prüfe ob mindestens ein Ticket ausgewählt wurde
                if (empty($ticketData)) {
                    throw new \Exception("Bitte wählen Sie mindestens ein Ticket aus.");
                }

                // Prüfe die Gesamtanzahl gegen max_attendees der Veranstaltung
                $totalQuantity = array_sum(array_column($ticketData, 'quantity'));
                if ($event->max_attendees) {
                    $availableSlots = $event->availableTickets();
                    if ($totalQuantity > $availableSlots) {
                        throw new \Exception("Nicht genügend Plätze verfügbar. Noch {$availableSlots} von {$event->max_attendees} Plätzen frei.");
                    }
                }

                // Rabattcode prüfen
                $discount = 0;
                $discountCodeId = null;

                if ($request->discount_code) {
                    $discountCode = DiscountCode::where('code', $request->discount_code)
                        ->where(function ($q) use ($event) {
                            $q->whereNull('event_id')
                                ->orWhere('event_id', $event->id);
                        })
                        ->first();

                    if ($discountCode && $discountCode->isValid()) {
                        $discount = $discountCode->calculateDiscount($subtotal);
                        $discountCodeId = $discountCode->id;
                        $discountCode->increment('usage_count');
                    }
                }

                $total = $subtotal - $discount;

                // Generiere E-Mail-Verifizierungs-Token für Gäste (ohne Account)
                $emailVerificationToken = null;
                if (!auth()->check()) {
                    $emailVerificationToken = \Illuminate\Support\Str::random(60);
                }

                // Bei kostenlosen Tickets direkt bestätigen
                $isFree = $total == 0;
                $initialStatus = $isFree ? 'confirmed' : 'pending';
                $initialPaymentStatus = $isFree ? 'paid' : 'pending';
                $confirmedAt = $isFree ? now() : null;

                // Erstelle Buchung
                $booking = Booking::create([
                    'event_id' => $event->id,
                    'user_id' => auth()->id(),
                    'customer_name' => $request->customer_name,
                    'customer_email' => $request->customer_email,
                    'customer_phone' => $request->customer_phone,
                    'billing_address' => $request->billing_address,
                    'billing_postal_code' => $request->billing_postal_code,
                    'billing_city' => $request->billing_city,
                    'billing_country' => $request->billing_country,
                    'email_verification_token' => $emailVerificationToken,
                    'subtotal' => $subtotal,
                    'discount' => $discount,
                    'total' => $total,
                    'status' => $initialStatus,
                    'payment_status' => $initialPaymentStatus,
                    'payment_method' => $request->payment_method ?? 'invoice',
                    'confirmed_at' => $confirmedAt,
                    'discount_code_id' => $discountCodeId,
                    'additional_data' => $request->except(['_token', 'tickets', 'customer_name', 'customer_email', 'customer_phone', 'billing_address', 'billing_postal_code', 'billing_city', 'billing_country', 'discount_code', 'payment_method']),
                ]);

                // Erstelle Buchungspositionen
                foreach ($ticketData as $data) {
                    for ($i = 0; $i < $data['quantity']; $i++) {
                        BookingItem::create([
                            'booking_id' => $booking->id,
                            'ticket_type_id' => $data['ticket_type']->id,
                            'price' => $data['price'],
                            'quantity' => 1,
                            // Bei nur einem Ticket automatisch den Käufer als Teilnehmer eintragen
                            'attendee_name' => $totalQuantity === 1 ? $request->customer_name : null,
                            'attendee_email' => $totalQuantity === 1 ? $request->customer_email : null,
                        ]);
                    }

                    // Aktualisiere verkaufte Menge
                    $data['ticket_type']->increment('quantity_sold', $data['quantity']);
                }

                // Wenn nur ein Ticket, markiere als personalisiert
                if ($totalQuantity === 1) {
                    $booking->update([
                        'tickets_personalized' => true,
                        'tickets_personalized_at' => now(),
                    ]);
                }

                // Lade Beziehungen für E-Mail-Versand
                $booking->load([
                    'items.ticketType',
                    'event.organization.users',
                    'event.category'
                ]);

                // PayPal-Zahlung: Redirect zu PayPal
                if (!$isFree && $request->payment_method === 'paypal') {
                    // Initialize PayPal service with organization credentials
                    $paypalService = new \App\Services\PayPalService($event->organization);

                    if (!$paypalService->isAvailable()) {
                        throw new \Exception('PayPal ist für diesen Veranstalter nicht konfiguriert.');
                    }

                    $paypalOrder = $paypalService->createOrder($booking);

                    if (!$paypalOrder || !isset($paypalOrder['id'])) {
                        throw new \Exception('PayPal-Bestellung konnte nicht erstellt werden. Bitte versuchen Sie es erneut oder wählen Sie eine andere Zahlungsmethode.');
                    }

                    // Find approval URL
                    $approvalUrl = null;
                    foreach ($paypalOrder['links'] ?? [] as $link) {
                        if ($link['rel'] === 'approve') {
                            $approvalUrl = $link['href'];
                            break;
                        }
                    }

                    if (!$approvalUrl) {
                        throw new \Exception('PayPal-Zahlungs-URL konnte nicht gefunden werden.');
                    }

                    // Store PayPal order ID temporarily
                    $booking->update([
                        'additional_data' => array_merge($booking->additional_data ?? [], [
                            'paypal_order_id' => $paypalOrder['id'],
                        ]),
                    ]);

                    // Redirect to PayPal
                    return redirect()->away($approvalUrl);
                }

                // Sende entsprechende E-Mail
                if ($isFree) {
                    // Bei kostenlosen Tickets: Direkt Tickets versenden
                    Mail::to($booking->customer_email)->send(new \App\Mail\PaymentConfirmed($booking));
                    $successMessage = 'Buchung erfolgreich! Ihre Tickets wurden per E-Mail versendet.';
                } else {
                    // Bei kostenpflichtigen Tickets: Zahlungsaufforderung mit Rechnung
                    Mail::to($booking->customer_email)->send(new \App\Mail\BookingConfirmation($booking));
                    $successMessage = 'Buchung erfolgreich erstellt! Eine Rechnung mit Zahlungsinformationen wurde per E-Mail versendet.';
                }

                // Benachrichtige Organizer über neue Buchung
                if ($event->user) {
                    $notificationPreferences = $event->user->notification_preferences ?? [];
                    if (is_array($notificationPreferences) && ($notificationPreferences['booking_notifications'] ?? true)) {
                        $event->user->notify(new \App\Notifications\NewBookingNotification($booking));
                    }
                }

                // Mark waitlist entry as converted if user came from waitlist
                if (auth()->check() || $booking->customer_email) {
                    $waitlistEntry = \App\Models\EventWaitlist::where('event_id', $event->id)
                        ->where('email', $booking->customer_email)
                        ->where('status', 'notified')
                        ->first();

                    if ($waitlistEntry) {
                        $waitlistEntry->markAsConverted();
                    }
                }


                // Zusätzliche Info für Gäste
                if (!auth()->check()) {
                    $successMessage .= ' Bitte verifizieren Sie Ihre E-Mail-Adresse über den Link in der E-Mail.';
                }

                return redirect()->route('bookings.show', $booking->booking_number)
                    ->with('success', $successMessage);
            });
        } catch (\Exception $e) {
            Log::error('Booking store error', [
                'event_id' => $event->id,
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->except(['_token']),
            ]);
            return back()->withInput()
                ->with('error', 'Fehler beim Erstellen der Buchung: ' . $e->getMessage());
        }
    }

    public function show($bookingNumber)
    {
        $booking = Booking::where('booking_number', $bookingNumber)
            ->with(['event.organization', 'items.ticketType'])
            ->firstOrFail();

        // Prüfe Zugriff - erlaube wenn:
        // 1. Eingeloggt UND Eigentümer der Buchung (user_id match)
        // 2. Gast mit Session-Zugriff (nach E-Mail-Verifizierung)
        $hasAccess = false;

        // Eingeloggte User mit matching user_id
        if (auth()->check() && auth()->id() === $booking->user_id) {
            $hasAccess = true;
        }

        // Gäste mit Session-Zugriff (nach E-Mail-Verifizierung)
        if (session()->has('booking_access_' . $booking->id)) {
            $hasAccess = true;
        }

        if (!$hasAccess) {
            return redirect()->route('bookings.verify', $bookingNumber)
                ->with('error', 'Bitte verifizieren Sie Ihre E-Mail-Adresse, um auf die Buchungsdetails zuzugreifen.');
        }

        return view('bookings.show', compact('booking'));
    }

    public function verify($bookingNumber)
    {
        $booking = Booking::where('booking_number', $bookingNumber)->firstOrFail();
        return view('bookings.verify', compact('booking'));
    }

    public function verifyEmail(Request $request, $bookingNumber)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $booking = Booking::where('booking_number', $bookingNumber)->firstOrFail();

        if ($booking->customer_email === $request->email) {
            session()->put('booking_access_' . $booking->id, true);
            return redirect()->route('bookings.show', $bookingNumber);
        }

        return back()->withErrors(['email' => 'E-Mail-Adresse stimmt nicht überein.']);
    }

    public function cancel(Request $request, $bookingNumber)
    {
        $booking = Booking::where('booking_number', $bookingNumber)->firstOrFail();

        // Check if user is authorized to cancel this booking
        if ($booking->user_id && $booking->user_id !== auth()->id()) {
            abort(403, 'Sie sind nicht berechtigt, diese Buchung zu stornieren.');
        }

        // Prüfe ob Stornierung möglich ist (z.B. 24h vor Event)
        if ($booking->event->start_date->subHours(24)->isPast()) {
            return back()->with('error', 'Stornierung nicht mehr möglich.');
        }

        $booking->update([
            'status' => 'cancelled',
            'cancelled_at' => now(),
        ]);

        // Gebe Tickets zurück
        foreach ($booking->items as $item) {
            $item->ticketType->decrement('quantity_sold', $item->quantity);
        }

        // Sende Stornierungsbestätigung per Email
        Mail::to($booking->customer_email)->send(new \App\Mail\BookingCancellation($booking));

        // Benachrichtige Organizer über Stornierung
        if ($booking->event->user) {
            $notificationPreferences = $booking->event->user->notification_preferences ?? [];
            if (is_array($notificationPreferences) && ($notificationPreferences['booking_notifications'] ?? true)) {
                $booking->event->user->notify(new \App\Notifications\BookingCancelledNotification($booking));
            }
        }

        // Notify waitlist - Fallback if observer doesn't trigger
        $this->notifyWaitlist($booking);

        return back()->with('success', 'Buchung erfolgreich storniert.');
    }

    public function validateDiscountCode(Request $request)
    {
        $request->validate([
            'code' => 'required|string',
            'event_id' => 'required|exists:events,id',
            'subtotal' => 'required|numeric|min:0',
        ]);

        $discountCode = DiscountCode::where('code', $request->code)
            ->where(function ($q) use ($request) {
                $q->whereNull('event_id')
                    ->orWhere('event_id', $request->event_id);
            })
            ->first();

        if (!$discountCode) {
            return response()->json([
                'valid' => false,
                'message' => 'Ungültiger Rabattcode',
            ], 404);
        }

        if (!$discountCode->isValid()) {
            return response()->json([
                'valid' => false,
                'message' => 'Dieser Rabattcode ist nicht mehr gültig.',
            ], 400);
        }

        $discount = $discountCode->calculateDiscount($request->subtotal);

        return response()->json([
            'valid' => true,
            'discount' => $discount,
            'discount_formatted' => number_format($discount, 2, ',', '.') . ' €',
            'type' => $discountCode->discount_type,
            'value' => $discountCode->discount_value,
        ]);
    }

    public function downloadInvoice($bookingNumber)
    {
        $booking = Booking::where('booking_number', $bookingNumber)
            ->with(['event.organization', 'items.ticketType'])
            ->firstOrFail();

        // Prüfe Zugriff - erlaube wenn eingeloggt und Eigentümer ODER Gast mit Session-Zugriff
        $hasAccess = false;

        if (auth()->check() && auth()->id() === $booking->user_id) {
            $hasAccess = true;
        }

        if (session()->has('booking_access_' . $booking->id)) {
            $hasAccess = true;
        }

        if (!$hasAccess) {
            abort(403, 'Kein Zugriff auf diese Rechnung');
        }

        $pdfService = app(TicketPdfService::class);
        return $pdfService->downloadInvoice($booking);
    }

    public function downloadTicket($bookingNumber)
    {
        $booking = Booking::where('booking_number', $bookingNumber)
            ->with(['event.organization', 'items.ticketType'])
            ->firstOrFail();

        // Prüfe Zugriff - erlaube wenn eingeloggt und Eigentümer ODER Gast mit Session-Zugriff
        $hasAccess = false;

        if (auth()->check() && auth()->id() === $booking->user_id) {
            $hasAccess = true;
        }

        if (session()->has('booking_access_' . $booking->id)) {
            $hasAccess = true;
        }

        if (!$hasAccess) {
            abort(403, 'Kein Zugriff auf dieses Ticket');
        }

        // Prüfe, ob Ticket heruntergeladen werden darf
        // Nur für bestätigte Buchungen ODER wenn die Buchung kostenlos ist (Gesamtpreis = 0)
        if ($booking->status !== 'confirmed' && $booking->total > 0) {
            abort(403, 'Tickets können nur für bestätigte und bezahlte Buchungen heruntergeladen werden.');
        }

        $pdfService = app(TicketPdfService::class);

        // Verwende individuelle Tickets für alle Buchungen
        $filename = "Tickets_{$booking->booking_number}.pdf";
        return response($pdfService->getAllIndividualTicketsContent($booking), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    public function downloadCertificate($bookingNumber)
    {
        $booking = Booking::where('booking_number', $bookingNumber)
            ->with(['event.organization', 'items.ticketType'])
            ->firstOrFail();

        // Prüfe Zugriff (nur für Buchungsinhaber oder eingeloggte User)
        if (!auth()->check() ||
            (auth()->id() !== $booking->user_id &&
             !session()->has('booking_access_' . $booking->id))) {
            abort(403, 'Kein Zugriff auf dieses Zertifikat');
        }

        $certificateService = app(\App\Services\CertificateService::class);

        // Check if certificate can be generated
        if (!$certificateService->canGenerateCertificate($booking)) {
            return back()->with('error', 'Zertifikat kann nur für bestätigte und abgeschlossene Veranstaltungen heruntergeladen werden.');
        }

        return $certificateService->downloadCertificate($booking);
    }

    /**
     * Download individual certificate for a specific attendee
     */
    public function downloadIndividualCertificate($bookingNumber, $itemId)
    {
        $booking = Booking::where('booking_number', $bookingNumber)
            ->with(['event.organization', 'items.ticketType'])
            ->firstOrFail();

        // Prüfe Zugriff
        if (!$this->hasBookingAccess($booking)) {
            abort(403, 'Kein Zugriff auf dieses Zertifikat');
        }

        // Finde das BookingItem
        $item = $booking->items()->findOrFail($itemId);

        $certificateService = app(\App\Services\CertificateService::class);

        // Check if certificate can be generated for this item
        if (!$certificateService->canGenerateIndividualCertificate($item)) {
            return back()->with('error', 'Zertifikat kann nur für eingecheckte Teilnehmer nach Ende der Veranstaltung heruntergeladen werden.');
        }

        return $certificateService->downloadIndividualCertificate($item);
    }

    /**
     * Download iCal file for booking
     */
    public function downloadIcal($bookingNumber)
    {
        $booking = Booking::where('booking_number', $bookingNumber)
            ->with(['event.organization'])
            ->firstOrFail();

        // Prüfe Zugriff (nur für Buchungsinhaber)
        if (!auth()->check() ||
            (auth()->id() !== $booking->user_id &&
             !session()->has('booking_access_' . $booking->id))) {
            abort(403, 'Kein Zugriff auf diesen Kalender-Export');
        }

        $calendarService = app(\App\Services\CalendarService::class);
        return $calendarService->downloadBookingIcal($booking);
    }

    /**
     * Verify email address for guest booking via token (from email link)
     */
    public function verifyEmailToken($bookingNumber, $token)
    {
        $booking = Booking::where('booking_number', $bookingNumber)
            ->where('email_verification_token', $token)
            ->firstOrFail();

        // Check if already verified
        if ($booking->email_verified_at) {
            return redirect()->route('bookings.show', $booking->booking_number)
                ->with('info', 'E-Mail-Adresse wurde bereits verifiziert.');
        }

        // Verify email
        $booking->update([
            'email_verified_at' => now(),
            'email_verification_token' => null,
        ]);

        // Allow access to booking details via session
        session()->put('booking_access_' . $booking->id, true);

        return redirect()->route('bookings.show', $booking->booking_number)
            ->with('success', 'E-Mail-Adresse erfolgreich verifiziert! Sie können nun auf Ihre Buchungsdetails zugreifen.');
    }

    /**
     * Notify waitlist when booking is cancelled
     */
    protected function notifyWaitlist(Booking $booking)
    {
        $event = $booking->event;

        // Calculate freed tickets
        $freedTickets = $booking->items->sum('quantity');

        if ($freedTickets > 0) {
            // Find waiting entries that could fit
            $waitingEntries = \App\Models\EventWaitlist::where('event_id', $event->id)
                ->waiting()
                ->notExpired()
                ->where('quantity', '<=', $freedTickets)
                ->orderBy('created_at')
                ->limit(5)
                ->get();

            $remainingTickets = $freedTickets;
            $notifiedCount = 0;

            foreach ($waitingEntries as $entry) {
                if ($remainingTickets >= $entry->quantity) {
                    $entry->markAsNotified();

                    // Send notification
                    try {
                        Mail::to($entry->email)->send(new \App\Mail\WaitlistTicketAvailable($entry));
                        $remainingTickets -= $entry->quantity;
                        $notifiedCount++;
                    } catch (\Exception $e) {
                        Log::error('Failed to send waitlist notification', [
                            'waitlist_id' => $entry->id,
                            'error' => $e->getMessage()
                        ]);
                    }
                }

                if ($remainingTickets <= 0) {
                    break;
                }
            }

            if ($notifiedCount > 0) {
                Log::info("Waitlist notifications sent from BookingController", [
                    'event_id' => $event->id,
                    'booking_id' => $booking->id,
                    'freed_tickets' => $freedTickets,
                    'notified' => $notifiedCount
                ]);
            }
        }
    }

    /**
     * Show personalization form for tickets
     */
    public function personalizeTickets($bookingNumber)
    {
        $booking = Booking::where('booking_number', $bookingNumber)
            ->with(['event', 'items.ticketType'])
            ->firstOrFail();

        // Check access
        if (!$this->hasBookingAccess($booking)) {
            return redirect()->route('bookings.verify', $bookingNumber)
                ->with('error', 'Bitte verifizieren Sie Ihre E-Mail-Adresse.');
        }

        // Check if personalization is needed
        if (!$booking->needsPersonalization()) {
            return redirect()->route('bookings.show', $bookingNumber)
                ->with('info', 'Diese Tickets sind bereits personalisiert oder benötigen keine Personalisierung.');
        }

        return view('bookings.personalize', compact('booking'));
    }

    /**
     * Save personalized ticket information
     */
    public function savePersonalization(Request $request, $bookingNumber)
    {
        $booking = Booking::where('booking_number', $bookingNumber)
            ->with(['event', 'items'])
            ->firstOrFail();

        // Check access
        if (!$this->hasBookingAccess($booking)) {
            abort(403, 'Nicht berechtigt.');
        }

        // Validate
        $request->validate([
            'attendees' => 'required|array',
            'attendees.*.attendee_name' => 'required|string|max:255',
            'attendees.*.attendee_email' => 'required|email|max:255',
        ], [
            'attendees.*.attendee_name.required' => 'Name ist erforderlich.',
            'attendees.*.attendee_email.required' => 'E-Mail ist erforderlich.',
            'attendees.*.attendee_email.email' => 'Bitte eine gültige E-Mail-Adresse eingeben.',
        ]);

        try {
            DB::transaction(function () use ($request, $booking) {
                foreach ($request->attendees as $itemId => $attendeeData) {
                    $item = $booking->items()->find($itemId);
                    if ($item) {
                        $item->update([
                            'attendee_name' => $attendeeData['attendee_name'],
                            'attendee_email' => $attendeeData['attendee_email'],
                        ]);
                    }
                }

                // Mark booking as personalized
                $booking->update([
                    'tickets_personalized' => true,
                    'tickets_personalized_at' => now(),
                ]);

                // Send tickets if payment is already confirmed
                if ($booking->payment_status === 'paid' && !$booking->event->isOnline()) {
                    Mail::to($booking->customer_email)
                        ->send(new \App\Mail\PaymentConfirmed($booking));
                }
            });

            return redirect()->route('bookings.show', $bookingNumber)
                ->with('success', 'Tickets erfolgreich personalisiert! Die Tickets wurden per E-Mail versendet.');
        } catch (\Exception $e) {
            Log::error('Fehler bei Ticket-Personalisierung', [
                'booking_number' => $bookingNumber,
                'error' => $e->getMessage(),
            ]);

            return back()->withInput()
                ->with('error', 'Fehler beim Speichern: ' . $e->getMessage());
        }
    }

    /**
     * Check if user has access to booking
     */
    protected function hasBookingAccess(Booking $booking): bool
    {
        // Logged in user with matching user_id
        if (auth()->check() && auth()->id() === $booking->user_id) {
            return true;
        }

        // Guest with session access (after email verification)
        if (session()->has('booking_access_' . $booking->id)) {
            return true;
        }

        return false;
    }
}
