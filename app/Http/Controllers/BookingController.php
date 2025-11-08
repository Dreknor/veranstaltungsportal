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
use Illuminate\Support\Facades\Mail;

class BookingController extends Controller
{
    public function create(Event $event)
    {
        // Check if event has any ticket types, if not create a default one
        if ($event->ticketTypes()->count() === 0) {
            // Create default ticket type based on event price_from or make it free
            $price = $event->price_from ?? 0;

            TicketType::create([
                'event_id' => $event->id,
                'name' => $price > 0 ? 'Standard-Ticket' : 'Kostenlose Teilnahme',
                'description' => 'Regulärer Zugang zur Veranstaltung',
                'price' => $price,
                'quantity' => $event->max_attendees,
                'quantity_sold' => 0,
                'is_available' => true,
                'min_per_order' => 1,
                'max_per_order' => $event->max_attendees ? min(10, $event->max_attendees) : 10,
            ]);
        }

        $ticketTypes = $event->ticketTypes()
            ->where('is_available', true)
            ->get()
            ->filter(function ($ticket) {
                return $ticket->isOnSale() && $ticket->availableQuantity() > 0;
            });

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
        $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'required|email|max:255',
            'customer_phone' => 'nullable|string|max:50',
            'billing_address' => 'required|string|max:255',
            'billing_postal_code' => 'required|string|max:20',
            'billing_city' => 'required|string|max:255',
            'billing_country' => 'required|string|max:100',
            'tickets' => 'required|array',
            'tickets.*.ticket_type_id' => 'required|exists:ticket_types,id',
            'tickets.*.quantity' => 'required|integer|min:1',
            'discount_code' => 'nullable|string',
        ]);

        try {
            return DB::transaction(function () use ($request, $event) {
                $subtotal = 0;
                $ticketData = [];

                // Validiere und berechne Tickets
                foreach ($request->tickets as $ticketInput) {
                    if ($ticketInput['quantity'] < 1) {
                        continue;
                    }

                    $ticketType = TicketType::findOrFail($ticketInput['ticket_type_id']);

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
                    'status' => 'pending',
                    'payment_status' => 'pending',
                    'discount_code_id' => $discountCodeId,
                    'additional_data' => $request->except(['_token', 'tickets', 'customer_name', 'customer_email', 'customer_phone', 'billing_address', 'billing_postal_code', 'billing_city', 'billing_country', 'discount_code']),
                ]);

                // Erstelle Buchungspositionen
                foreach ($ticketData as $data) {
                    for ($i = 0; $i < $data['quantity']; $i++) {
                        BookingItem::create([
                            'booking_id' => $booking->id,
                            'ticket_type_id' => $data['ticket_type']->id,
                            'price' => $data['price'],
                            'quantity' => 1,
                        ]);
                    }

                    // Aktualisiere verkaufte Menge
                    $data['ticket_type']->increment('quantity_sold', $data['quantity']);
                }

                // Sende Bestätigungs-Email mit Rechnung
                Mail::to($booking->customer_email)->send(new \App\Mail\BookingConfirmation($booking));

                $successMessage = 'Buchung erfolgreich erstellt! Eine Bestätigungs-Email mit Rechnung wurde versendet.';

                // Zusätzliche Info für Gäste
                if (!auth()->check()) {
                    $successMessage .= ' Bitte verifizieren Sie Ihre E-Mail-Adresse über den Link in der E-Mail.';
                }

                return redirect()->route('bookings.show', $booking->booking_number)
                    ->with('success', $successMessage);
            });
        } catch (\Exception $e) {
            return back()->withInput()
                ->with('error', $e->getMessage());
        }
    }

    public function show($bookingNumber)
    {
        $booking = Booking::where('booking_number', $bookingNumber)
            ->with(['event', 'items.ticketType'])
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
            ->with(['event', 'items.ticketType'])
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
            ->with(['event', 'items.ticketType'])
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

        $pdfService = app(TicketPdfService::class);
        return $pdfService->downloadTicket($booking);
    }

    public function downloadCertificate($bookingNumber)
    {
        $booking = Booking::where('booking_number', $bookingNumber)
            ->with(['event', 'items.ticketType'])
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
     * Download iCal file for booking
     */
    public function downloadIcal($bookingNumber)
    {
        $booking = Booking::where('booking_number', $bookingNumber)
            ->with(['event'])
            ->firstOrFail();

        // Prüfe Zugriff (nur für Buchungsinhaber)
        if (!auth()->check() ||
            (auth()->id() !== $booking->user_id &&
             !session()->has('booking_access_' . $booking->id))) {
            abort(403, 'Kein Zugriff auf diesen Kalender-Export');
        }

        $calendarService = app(\App\Services\CalendarService::class);
        return $calendarService->generateIcal($booking);
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
}
