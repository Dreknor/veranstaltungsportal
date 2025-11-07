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
        $ticketTypes = $event->ticketTypes()
            ->where('is_available', true)
            ->get()
            ->filter(function ($ticket) {
                return $ticket->isOnSale();
            });

        if ($ticketTypes->isEmpty()) {
            return redirect()->route('events.show', $event->slug)
                ->with('error', 'Keine Tickets verfügbar.');
        }

        return view('bookings.create', compact('event', 'ticketTypes'));
    }

    public function store(Request $request, Event $event)
    {
        $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'required|email|max:255',
            'customer_phone' => 'nullable|string|max:50',
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

                // Erstelle Buchung
                $booking = Booking::create([
                    'event_id' => $event->id,
                    'user_id' => auth()->id(),
                    'customer_name' => $request->customer_name,
                    'customer_email' => $request->customer_email,
                    'customer_phone' => $request->customer_phone,
                    'subtotal' => $subtotal,
                    'discount' => $discount,
                    'total' => $total,
                    'status' => 'pending',
                    'payment_status' => 'pending',
                    'discount_code_id' => $discountCodeId,
                    'additional_data' => $request->except(['_token', 'tickets', 'customer_name', 'customer_email', 'customer_phone', 'discount_code']),
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

                return redirect()->route('bookings.show', $booking->booking_number)
                    ->with('success', 'Buchung erfolgreich erstellt! Eine Bestätigungs-Email mit Rechnung wurde versendet.');
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

        // Prüfe Zugriff
        if (!auth()->check() ||
            (auth()->id() !== $booking->user_id &&
             $booking->customer_email !== request()->get('email'))) {
            if (!session()->has('booking_access_' . $booking->id)) {
                return redirect()->route('bookings.verify', $bookingNumber);
            }
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

        // Prüfe Zugriff (nur für Buchungsinhaber oder eingeloggte User)
        if (!auth()->check() ||
            (auth()->id() !== $booking->user_id &&
             !session()->has('booking_access_' . $booking->id))) {
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

        // Prüfe Zugriff (nur für Buchungsinhaber oder eingeloggte User)
        if (!auth()->check() ||
            (auth()->id() !== $booking->user_id &&
             !session()->has('booking_access_' . $booking->id))) {
            abort(403, 'Kein Zugriff auf dieses Ticket');
        }

        $pdfService = app(TicketPdfService::class);
        return $pdfService->downloadTicket($booking);
        return $invoiceService->downloadInvoice($booking);
    }
}
