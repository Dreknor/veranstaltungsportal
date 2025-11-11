<?php

namespace App\Http\Controllers\Organizer;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CheckInController extends Controller
{
    /**
     * Display check-in interface for an event
     */
    public function index(Event $event)
    {
        $this->authorize('update', $event);

        $bookings = $event->bookings()
            ->with(['user', 'items.ticketType'])
            ->where('payment_status', 'paid')
            ->where('status', 'confirmed')
            ->orderBy('checked_in', 'asc')
            ->orderBy('customer_name')
            ->get();

        $stats = [
            'total' => $bookings->count(),
            'checked_in' => $bookings->where('checked_in', true)->count(),
            'pending' => $bookings->where('checked_in', false)->count(),
        ];

        return view('organizer.check-in.index', compact('event', 'bookings', 'stats'));
    }

    /**
     * Check in a booking (manual)
     */
    public function checkIn(Request $request, Event $event, Booking $booking)
    {
        $this->authorize('update', $event);

        if ($booking->event_id !== $event->id) {
            return back()->with('error', 'Diese Buchung gehört nicht zu diesem Event.');
        }

        if (!$booking->canCheckIn()) {
            return back()->with('error', 'Diese Buchung kann nicht eingecheckt werden.');
        }

        $booking->checkIn(
            checkedInBy: auth()->user(),
            method: 'manual',
            notes: $request->input('notes')
        );

        return back()->with('status', 'Teilnehmer erfolgreich eingecheckt.');
    }

    /**
     * Undo check-in
     */
    public function undoCheckIn(Event $event, Booking $booking)
    {
        $this->authorize('update', $event);

        if ($booking->event_id !== $event->id) {
            return back()->with('error', 'Diese Buchung gehört nicht zu diesem Event.');
        }

        $booking->undoCheckIn();

        return back()->with('status', 'Check-in rückgängig gemacht.');
    }

    /**
     * Check in via QR code
     */
    public function scanQr(Request $request, Event $event)
    {
        $this->authorize('update', $event);

        $request->validate([
            'booking_number' => 'required|string',
        ]);

        $booking = Booking::where('booking_number', $request->booking_number)
            ->where('event_id', $event->id)
            ->first();

        if (!$booking) {
            return response()->json([
                'success' => false,
                'message' => 'Buchung nicht gefunden.',
            ], 404);
        }

        if ($booking->checked_in) {
            return response()->json([
                'success' => false,
                'message' => 'Bereits eingecheckt am ' . $booking->checked_in_at->format('d.m.Y H:i'),
                'booking' => [
                    'id' => $booking->id,
                    'booking_number' => $booking->booking_number,
                    'customer_name' => $booking->customer_name,
                    'checked_in' => true,
                    'checked_in_at' => $booking->checked_in_at,
                ],
            ], 400);
        }

        if (!$booking->canCheckIn()) {
            return response()->json([
                'success' => false,
                'message' => 'Diese Buchung kann nicht eingecheckt werden. Status: ' . $booking->status . ', Zahlung: ' . $booking->payment_status,
            ], 400);
        }

        $booking->checkIn(
            checkedInBy: auth()->user(),
            method: 'qr',
            notes: 'QR-Code gescannt'
        );

        return response()->json([
            'success' => true,
            'message' => 'Erfolgreich eingecheckt!',
            'booking' => [
                'id' => $booking->id,
                'booking_number' => $booking->booking_number,
                'customer_name' => $booking->customer_name,
                'customer_email' => $booking->customer_email,
                'checked_in' => true,
                'checked_in_at' => $booking->checked_in_at,
                'tickets_count' => $booking->items->sum('quantity'),
            ],
        ]);
    }

    /**
     * Bulk check-in
     */
    public function bulkCheckIn(Request $request, Event $event)
    {
        $this->authorize('update', $event);

        $request->validate([
            'booking_ids' => 'required|array',
            'booking_ids.*' => 'exists:bookings,id',
        ]);

        $bookings = Booking::whereIn('id', $request->booking_ids)
            ->where('event_id', $event->id)
            ->get();

        $checkedInCount = 0;
        $errors = [];

        foreach ($bookings as $booking) {
            if ($booking->canCheckIn()) {
                $booking->checkIn(
                    checkedInBy: auth()->user(),
                    method: 'manual',
                    notes: 'Bulk-Check-in'
                );
                $checkedInCount++;
            } else {
                $errors[] = $booking->customer_name . ' (Status: ' . $booking->status . ')';
            }
        }

        $message = $checkedInCount . ' Teilnehmer erfolgreich eingecheckt.';
        if (count($errors) > 0) {
            $message .= ' Fehler bei: ' . implode(', ', $errors);
        }

        return back()->with('status', $message);
    }

    /**
     * Export check-in list
     */
    public function exportCheckInList(Event $event)
    {
        $this->authorize('update', $event);

        $bookings = $event->bookings()
            ->with(['user', 'items.ticketType', 'checkedInBy'])
            ->where('payment_status', 'paid')
            ->where('status', 'confirmed')
            ->orderBy('checked_in', 'desc')
            ->orderBy('checked_in_at', 'desc')
            ->get();

        $filename = 'checkin-list-' . $event->slug . '-' . now()->format('Y-m-d') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($bookings) {
            $file = fopen('php://output', 'w');

            // UTF-8 BOM
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

            // Header
            fputcsv($file, [
                'Buchungsnummer',
                'Name',
                'E-Mail',
                'Telefon',
                'Tickets',
                'Status',
                'Eingecheckt',
                'Check-in Zeitpunkt',
                'Check-in von',
                'Check-in Methode',
                'Notizen',
            ], ';');

            // Data
            foreach ($bookings as $booking) {
                fputcsv($file, [
                    $booking->booking_number,
                    $booking->customer_name,
                    $booking->customer_email,
                    $booking->customer_phone ?? '',
                    $booking->items->sum('quantity'),
                    $booking->checked_in ? 'Ja' : 'Nein',
                    $booking->checked_in_at ? $booking->checked_in_at->format('d.m.Y H:i') : '',
                    $booking->checkedInBy?->fullName() ?? '',
                    $booking->check_in_method ?? '',
                    $booking->check_in_notes ?? '',
                ], ';');
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
