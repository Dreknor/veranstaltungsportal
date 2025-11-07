<?php

namespace App\Http\Controllers\Organizer;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Event;
use Illuminate\Http\Request;

class BookingManagementController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth']);
    }

    public function index(Request $request)
    {
        $query = Booking::whereHas('event', function ($q) {
            $q->where('user_id', auth()->id());
        })->with(['event', 'items']);

        // Filter nach Event
        if ($request->has('event_id')) {
            $query->where('event_id', $request->event_id);
        }

        // Filter nach Status
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        // Filter nach Zahlungsstatus
        if ($request->has('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        // Suche
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('booking_number', 'LIKE', "%{$search}%")
                    ->orWhere('customer_name', 'LIKE', "%{$search}%")
                    ->orWhere('customer_email', 'LIKE', "%{$search}%");
            });
        }

        $bookings = $query->latest()->paginate(20);

        $events = Event::where('user_id', auth()->id())->get();

        return view('organizer.bookings.index', compact('bookings', 'events'));
    }

    public function show(Booking $booking)
    {
        $this->authorize('view', $booking);

        $booking->load(['event', 'items.ticketType', 'user', 'discountCode']);

        return view('organizer.bookings.show', compact('booking'));
    }

    public function updateStatus(Request $request, Booking $booking)
    {
        $this->authorize('update', $booking);

        $request->validate([
            'status' => 'required|in:pending,confirmed,cancelled,completed',
        ]);

        $booking->update([
            'status' => $request->status,
            'confirmed_at' => $request->status === 'confirmed' ? now() : $booking->confirmed_at,
            'cancelled_at' => $request->status === 'cancelled' ? now() : $booking->cancelled_at,
        ]);

        // TODO: Send notification email

        return back()->with('success', 'Buchungsstatus aktualisiert!');
    }

    public function updatePaymentStatus(Request $request, Booking $booking)
    {
        $this->authorize('update', $booking);

        $request->validate([
            'payment_status' => 'required|in:pending,paid,refunded,failed',
        ]);

        $booking->update([
            'payment_status' => $request->payment_status,
        ]);

        // TODO: Send notification email

        return back()->with('success', 'Zahlungsstatus aktualisiert!');
    }

    public function export(Request $request)
    {
        $query = Booking::whereHas('event', function ($q) {
            $q->where('user_id', auth()->id());
        })->with(['event', 'items.ticketType']);

        if ($request->has('event_id')) {
            $query->where('event_id', $request->event_id);
        }

        $bookings = $query->get();

        $filename = 'bookings-' . now()->format('Y-m-d') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function() use ($bookings) {
            $file = fopen('php://output', 'w');

            // Header
            fputcsv($file, [
                'Buchungsnummer',
                'Event',
                'Kunde',
                'Email',
                'Telefon',
                'Anzahl Tickets',
                'Gesamt',
                'Status',
                'Zahlungsstatus',
                'Buchungsdatum',
            ]);

            // Daten
            foreach ($bookings as $booking) {
                fputcsv($file, [
                    $booking->booking_number,
                    $booking->event->title,
                    $booking->customer_name,
                    $booking->customer_email,
                    $booking->customer_phone,
                    $booking->items->count(),
                    number_format($booking->total, 2, ',', '.') . ' €',
                    $booking->status,
                    $booking->payment_status,
                    $booking->created_at->format('d.m.Y H:i'),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function checkIn(Request $request, Booking $booking)
    {
        $this->authorize('update', $booking);

        $request->validate([
            'ticket_id' => 'required|exists:booking_items,id',
        ]);

        $item = $booking->items()->findOrFail($request->ticket_id);

        $item->update([
            'checked_in' => true,
            'checked_in_at' => now(),
        ]);

        return back()->with('success', 'Ticket erfolgreich eingecheckt!');
    }
}

