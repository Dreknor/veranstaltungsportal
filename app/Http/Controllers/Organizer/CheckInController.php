<?php

namespace App\Http\Controllers\Organizer;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\BookingItem;
use App\Models\Event;
use App\Models\EventDate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CheckInController extends Controller
{
    /**
     * Display check-in interface for an event (ticket-level view)
     */
    public function index(Request $request, Event $event)
    {
        $this->authorize('update', $event);

        // Veranstaltungen mit mehreren Terminen: Check-in je Termin
        $dates = $event->hasMultipleDates() ? $event->dates()->where('is_cancelled', false)->get() : collect();
        $selectedDate = $this->resolveDate($event, $request);

        // Einzelne Tickets (BookingItems) laden – eine Zeile pro Ticket
        $items = BookingItem::whereHas('booking', function ($q) use ($event) {
                $q->where('event_id', $event->id)->readyForParticipation();
            })
            ->with(['booking', 'ticketType', 'attendances'])
            ->orderBy('checked_in', 'asc')
            ->orderBy('attendee_name')
            ->get()
            // Fallback-Sortierung nach Kundenname wenn kein Teilnehmername gesetzt
            ->sortBy(fn($item) => [$item->isCheckedInFor($selectedDate) ? 1 : 0, mb_strtolower($item->participantName())]);

        $checkedIn = $items->filter(fn ($item) => $item->isCheckedInFor($selectedDate))->count();
        $stats = [
            'total'       => $items->count(),
            'checked_in'  => $checkedIn,
            'pending'     => $items->count() - $checkedIn,
        ];

        return view('organizer.check-in.index', compact('event', 'items', 'stats', 'dates', 'selectedDate'));
    }

    /**
     * Ausgewählter Termin (Mehrfachtermine): Parameter, sonst heutiger, sonst nächster, sonst letzter Termin.
     */
    protected function resolveDate(Event $event, Request $request): ?EventDate
    {
        if (!$event->hasMultipleDates()) {
            return null;
        }

        $dates = $event->dates()->where('is_cancelled', false)->get();
        $id = $request->input('event_date_id', $request->query('date'));

        return ($id ? $dates->firstWhere('id', (int) $id) : null)
            ?? $dates->first(fn ($d) => $d->start_date->isToday())
            ?? $dates->first(fn ($d) => ($d->end_date ?? $d->start_date)->isFuture())
            ?? $dates->last();
    }

    /**
     * Check in a single booking item (ticket-level, manual)
     */
    public function checkInItem(Request $request, Event $event, BookingItem $item)
    {
        $this->authorize('update', $event);

        $booking = $item->booking;

        if ($booking->event_id !== $event->id) {
            return back()->with('error', 'Dieses Ticket gehört nicht zu diesem Event.');
        }

        $date = $this->resolveDate($event, $request);

        if ($item->isCheckedInFor($date)) {
            return back()->with('error', 'Dieses Ticket ist ' . ($date ? 'für diesen Termin ' : '') . 'bereits eingecheckt.');
        }

        if ($booking->status !== 'confirmed' || !$booking->isReadyForParticipation()) {
            return back()->with('error', 'Buchung nicht bestätigt oder nicht bezahlt (Status: ' . $booking->statusLabel() . ', Zahlung: ' . $booking->paymentStatusLabel() . ').');
        }

        $item->checkInFor($date, auth()->user());

        return back()->with('status', 'Ticket erfolgreich eingecheckt: ' . $item->participantName()
            . ($date ? ' (Termin ' . $date->start_date->format('d.m.Y') . ')' : ''));
    }

    /**
     * Undo check-in for a single booking item
     */
    public function undoCheckInItem(Request $request, Event $event, BookingItem $item)
    {
        $this->authorize('update', $event);

        if ($item->booking->event_id !== $event->id) {
            return back()->with('error', 'Dieses Ticket gehört nicht zu diesem Event.');
        }

        $item->undoCheckInFor($this->resolveDate($event, $request));

        return back()->with('status', 'Check-in rückgängig gemacht.');
    }

    /**
     * Check in a booking (manual, whole booking – kept for backward compat / QR fallback)
     */
    public function checkIn(Request $request, Event $event, Booking $booking)
    {
        $this->authorize('update', $event);

        if ($booking->event_id !== $event->id) {
            return back()->with('error', 'Diese Buchung gehört nicht zu diesem Event.');
        }

        if (!$booking->canCheckIn()) {
            $reason = '';
            if ($booking->status !== 'confirmed') {
                $reason = 'Buchung ist nicht bestätigt (aktuell: ' . $booking->statusLabel() . ')';
            } elseif (!$booking->isPaymentComplete()) {
                $reason = 'Zahlung ist nicht abgeschlossen (aktuell: ' . $booking->paymentStatusLabel() . ')';
            } elseif ($booking->checked_in) {
                $reason = 'Bereits eingecheckt am ' . $booking->checked_in_at->format('d.m.Y H:i');
            } elseif ($booking->event->start_date->isFuture() && !$booking->event->start_date->isToday()) {
                $reason = 'Event liegt noch in der Zukunft';
            }

            Log::warning('Check-In fehlgeschlagen', [
                'booking_id' => $booking->id,
                'booking_number' => $booking->booking_number,
                'status' => $booking->status,
                'payment_status' => $booking->payment_status,
                'checked_in' => $booking->checked_in,
                'reason' => $reason,
            ]);

            return back()->with('error', 'Diese Buchung kann nicht eingecheckt werden. ' . $reason);
        }

        try {
            $booking->checkIn(
                checkedInBy: auth()->user(),
                method: 'manual',
                notes: $request->input('notes')
            );

            Log::info('Check-In erfolgreich', [
                'booking_id' => $booking->id,
                'booking_number' => $booking->booking_number,
                'checked_in_by' => auth()->id(),
            ]);

            return back()->with('status', 'Teilnehmer erfolgreich eingecheckt: ' . $booking->customer_name);
        } catch (\Exception $e) {
            Log::error('Check-In Fehler', [
                'booking_id' => $booking->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return back()->with('error', 'Fehler beim Check-In: ' . $e->getMessage());
        }
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

        if ($date = $this->resolveDate($event, $request)) {
            return $this->scanForDate($booking, $date);
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
            ], 422);
        }

        if (!$booking->canCheckIn()) {
            return response()->json([
                'success' => false,
                'message' => 'Diese Buchung kann nicht eingecheckt werden. Status: ' . $booking->status . ', Zahlung: ' . $booking->payment_status,
            ], 422);
        }

        $booking->checkIn(
            checkedInBy: auth()->user(),
            method: 'qr',
            notes: 'QR-Code gescannt'
        );

        // Alle Tickets (BookingItems) dieser Buchung ebenfalls einchecken
        $booking->items()->where('checked_in', false)->update([
            'checked_in'    => true,
            'checked_in_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Erfolgreich eingecheckt!',
            'booking' => [
                'id'             => $booking->id,
                'booking_number' => $booking->booking_number,
                'customer_name'  => $booking->customer_name,
                'customer_email' => $booking->customer_email,
                'checked_in'     => true,
                'checked_in_at'  => $booking->checked_in_at,
                'tickets_count'  => $booking->items->count(),
            ],
        ]);
    }

    /**
     * QR-Check-in für einen einzelnen Termin einer Veranstaltung mit mehreren Terminen.
     */
    protected function scanForDate(Booking $booking, EventDate $date)
    {
        $booking->load('items.attendances');
        $label = 'Termin ' . $date->start_date->format('d.m.Y');

        if (!$booking->isReadyForParticipation()) {
            return response()->json([
                'success' => false,
                'message' => 'Diese Buchung kann nicht eingecheckt werden (' . $booking->statusLabel() . ', Zahlung: ' . $booking->paymentStatusLabel() . ').',
            ], 422);
        }

        $pending = $booking->items->reject(fn ($item) => $item->isCheckedInFor($date));
        if ($pending->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => "Bereits für {$label} eingecheckt.",
            ], 422);
        }

        foreach ($pending as $item) {
            $item->checkInFor($date, auth()->user());
        }

        return response()->json([
            'success' => true,
            'message' => "Erfolgreich eingecheckt ({$label})!",
            'booking' => [
                'id'             => $booking->id,
                'booking_number' => $booking->booking_number,
                'customer_name'  => $booking->customer_name,
                'customer_email' => $booking->customer_email,
                'checked_in'     => true,
                'checked_in_at'  => now(),
                'tickets_count'  => $pending->count(),
            ],
        ]);
    }

    /**
     * Bulk check-in: prüft item_ids (Ticket-Ebene)
     */
    public function bulkCheckIn(Request $request, Event $event)
    {
        $this->authorize('update', $event);

        $request->validate([
            'item_ids'   => 'required|array',
            'item_ids.*' => 'exists:booking_items,id',
        ]);

        $items = BookingItem::whereIn('id', $request->item_ids)
            ->whereHas('booking', function ($q) use ($event) {
                $q->where('event_id', $event->id)->readyForParticipation();
            })
            ->with('booking')
            ->get();

        $date = $this->resolveDate($event, $request);
        $checkedInCount = 0;
        $skipped        = [];

        foreach ($items as $item) {
            if ($item->isCheckedInFor($date)) {
                $skipped[] = $item->participantName() . ' (bereits eingecheckt)';
                continue;
            }
            $item->checkInFor($date, auth()->user());
            $checkedInCount++;
        }

        $message = $checkedInCount . ' Ticket(s) erfolgreich eingecheckt.';
        if (count($skipped) > 0) {
            $message .= ' Übersprungen: ' . implode(', ', $skipped);
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
            ->readyForParticipation()
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

    /**
     * Check in a booking directly (for QR code URLs)
     * The event is determined from the booking
     */
    public function checkInByBooking(Request $request, Booking $booking)
    {
        $event = $booking->event;

        // Authorization: User must be the organizer of the event
        $this->authorize('update', $event);

        if (!$booking->canCheckIn()) {
            $reason = '';
            if ($booking->status !== 'confirmed') {
                $reason = 'Buchung ist nicht bestätigt (aktuell: ' . $booking->statusLabel() . ')';
            } elseif (!$booking->isPaymentComplete()) {
                $reason = 'Zahlung ist nicht abgeschlossen (aktuell: ' . $booking->paymentStatusLabel() . ')';
            } elseif ($booking->checked_in) {
                $reason = 'Bereits eingecheckt am ' . $booking->checked_in_at->format('d.m.Y H:i');
            } elseif ($booking->event->start_date->isFuture() && !$booking->event->start_date->isToday()) {
                $reason = 'Event liegt noch in der Zukunft';
            }

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Diese Buchung kann nicht eingecheckt werden. ' . $reason,
                    'booking' => [
                        'id' => $booking->id,
                        'booking_number' => $booking->booking_number,
                        'customer_name' => $booking->customer_name,
                        'checked_in' => $booking->checked_in,
                        'checked_in_at' => $booking->checked_in_at,
                    ],
                ], 400);
            }

            return back()->with('error', 'Diese Buchung kann nicht eingecheckt werden. ' . $reason);
        }

        try {
            $booking->checkIn(
                checkedInBy: auth()->user(),
                method: $request->input('method', 'qr'),
                notes: $request->input('notes', 'QR-Code Check-in')
            );

            Log::info('Check-In erfolgreich (via QR)', [
                'booking_id' => $booking->id,
                'booking_number' => $booking->booking_number,
                'event_id' => $event->id,
                'checked_in_by' => auth()->id(),
            ]);

            if ($request->expectsJson()) {
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
                        'event' => [
                            'id' => $event->id,
                            'title' => $event->title,
                        ],
                    ],
                ]);
            }

            return redirect()
                ->route('organizer.check-in.index', ['event' => $event])
                ->with('status', 'Teilnehmer erfolgreich eingecheckt: ' . $booking->customer_name);

        } catch (\Exception $e) {
            Log::error('Check-In Fehler (via QR)', [
                'booking_id' => $booking->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Fehler beim Check-In: ' . $e->getMessage(),
                ], 500);
            }

            return back()->with('error', 'Fehler beim Check-In: ' . $e->getMessage());
        }
    }

    /**
     * Get check-in statistics for an event (ticket-level)
     */
    public function stats(Request $request, Event $event)
    {
        $this->authorize('update', $event);

        if ($date = $this->resolveDate($event, $request)) {
            $items = BookingItem::whereHas('booking', fn ($q) => $q->where('event_id', $event->id)->readyForParticipation())
                ->with('attendances')
                ->get();
            $checkedIn = $items->filter(fn ($item) => $item->isCheckedInFor($date))->count();

            return response()->json([
                'total'      => $items->count(),
                'checked_in' => $checkedIn,
                'pending'    => $items->count() - $checkedIn,
            ]);
        }

        $base = BookingItem::whereHas('booking', function ($q) use ($event) {
            $q->where('event_id', $event->id)->readyForParticipation();
        });

        $stats = [
            'total'      => (clone $base)->count(),
            'checked_in' => (clone $base)->where('checked_in', true)->count(),
            'pending'    => (clone $base)->where('checked_in', false)->count(),
        ];

        return response()->json($stats);
    }
}
