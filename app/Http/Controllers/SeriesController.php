<?php

namespace App\Http\Controllers;

use App\Models\EventSeries;
use App\Models\Booking;
use App\Models\BookingItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class SeriesController extends Controller
{
    /**
     * Display the specified series (public view)
     */
    public function show(EventSeries $series)
    {
        // Load events and relations
        $series->load(['events' => function($query) {
            $query->orderBy('series_position');
        }, 'category', 'user']);

        // Check if series is active
        if (!$series->is_active) {
            abort(404, 'Diese Veranstaltungsreihe ist nicht verfügbar.');
        }

        return view('series.show', compact('series'));
    }

    /**
     * Show booking form for series
     */
    public function book(EventSeries $series)
    {
        // Load events
        $series->load(['events' => function($query) {
            $query->orderBy('series_position');
        }, 'category']);

        // Check if series is active and bookable
        if (!$series->is_active) {
            return back()->with('error', 'Diese Veranstaltungsreihe ist nicht buchbar.');
        }

        // Check if all events exist
        if ($series->events->count() === 0) {
            return back()->with('error', 'Für diese Veranstaltungsreihe sind noch keine Termine festgelegt.');
        }

        return view('series.book', compact('series'));
    }

    /**
     * Store the series booking
     */
    public function store(Request $request, EventSeries $series)
    {
        // Validate input
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:50',
            'notes' => 'nullable|string|max:1000',
            'terms' => 'required|accepted',
        ]);

        try {
            return DB::transaction(function () use ($request, $series, $validated) {
                // Load events
                $series->load(['events' => function($query) {
                    $query->orderBy('series_position');
                }]);

                // Check if series is still active
                if (!$series->is_active) {
                    throw new \Exception('Diese Veranstaltungsreihe ist nicht mehr verfügbar.');
                }

                // Check if events exist
                if ($series->events->count() === 0) {
                    throw new \Exception('Für diese Veranstaltungsreihe sind keine Termine festgelegt.');
                }

                $customerName = $validated['first_name'] . ' ' . $validated['last_name'];
                $customerEmail = $validated['email'];
                $customerPhone = $validated['phone'] ?? null;

                // Erstelle eine Buchung für jedes Event in der Serie
                $bookings = [];

                foreach ($series->events as $event) {
                    // Erstelle oder hole Standard-Ticket-Type für das Event
                    $ticketType = $event->ticketTypes()->first();

                    if (!$ticketType) {
                        // Erstelle kostenlosen Standard-Ticket-Type
                        $ticketType = \App\Models\TicketType::create([
                            'event_id' => $event->id,
                            'name' => 'Kostenlose Teilnahme (Reihe)',
                            'description' => 'Teil der Veranstaltungsreihe: ' . $series->title,
                            'price' => 0,
                            'quantity' => $event->max_attendees,
                            'quantity_sold' => 0,
                            'is_available' => true,
                            'min_per_order' => 1,
                            'max_per_order' => 1,
                        ]);
                    }

                    // Bei kostenlosen Tickets direkt bestätigen
                    $isFree = $ticketType->price == 0;
                    $initialStatus = $isFree ? 'confirmed' : 'pending';
                    $initialPaymentStatus = $isFree ? 'paid' : 'pending';
                    $confirmedAt = $isFree ? now() : null;

                    // Erstelle Buchung für diesen Termin
                    $booking = Booking::create([
                        'event_id' => $event->id,
                        'user_id' => auth()->id(),
                        'customer_name' => $customerName,
                        'customer_email' => $customerEmail,
                        'customer_phone' => $customerPhone,
                        'billing_address' => $request->billing_address ?? 'N/A',
                        'billing_postal_code' => $request->billing_postal_code ?? '00000',
                        'billing_city' => $request->billing_city ?? 'N/A',
                        'billing_country' => $request->billing_country ?? 'Deutschland',
                        'subtotal' => $ticketType->price,
                        'discount' => 0,
                        'total' => $ticketType->price,
                        'status' => $initialStatus,
                        'payment_status' => $initialPaymentStatus,
                        'confirmed_at' => $confirmedAt,
                        'additional_data' => [
                            'series_id' => $series->id,
                            'series_title' => $series->title,
                            'series_position' => $event->series_position,
                            'notes' => $validated['notes'] ?? null,
                        ],
                    ]);

                    // Erstelle Buchungsposition
                    BookingItem::create([
                        'booking_id' => $booking->id,
                        'ticket_type_id' => $ticketType->id,
                        'price' => $ticketType->price,
                        'quantity' => 1,
                    ]);

                    // Aktualisiere verkaufte Menge
                    $ticketType->increment('quantity_sold');

                    $bookings[] = $booking;
                }

                // Sende Bestätigungs-E-Mail für die erste Buchung (repräsentiert die gesamte Serie)
                if (!empty($bookings)) {
                    $firstBooking = $bookings[0];

                    // Kostenlose Tickets = direkt bestätigt
                    if ($firstBooking->total == 0) {
                        Mail::to($customerEmail)->send(new \App\Mail\BookingConfirmation($firstBooking));

                        return redirect()->route('bookings.show', $firstBooking->booking_number)
                            ->with('success', 'Buchung erfolgreich! Sie haben sich für alle ' . count($bookings) . ' Termine angemeldet. Eine Bestätigung wurde per E-Mail versendet.');
                    } else {
                        Mail::to($customerEmail)->send(new \App\Mail\BookingConfirmation($firstBooking));

                        return redirect()->route('bookings.show', $firstBooking->booking_number)
                            ->with('success', 'Buchung erfolgreich! Bitte überweisen Sie den Betrag. Details wurden per E-Mail versendet.');
                    }
                }

                throw new \Exception('Fehler beim Erstellen der Buchungen.');
            });
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Fehler beim Buchen: ' . $e->getMessage());
        }
    }
}

