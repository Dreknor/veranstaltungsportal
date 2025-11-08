<?php

namespace App\Http\Controllers\Organizer;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class BookingManagementController extends Controller
{

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

        $oldPaymentStatus = $booking->payment_status;

        $booking->update([
            'payment_status' => $request->payment_status,
        ]);

        // Wenn Zahlung auf "paid" gesetzt wird, Tickets per E-Mail versenden
        if ($request->payment_status === 'paid' && $oldPaymentStatus !== 'paid') {
            try {
                \Illuminate\Support\Facades\Mail::to($booking->customer_email)
                    ->send(new \App\Mail\PaymentConfirmed($booking));

                return back()->with('success', 'Zahlungsstatus aktualisiert und Tickets per E-Mail versendet!');
            } catch (\Exception $e) {
                Log::error('Fehler beim Senden der Zahlungsbestätigungs-E-Mail: ',[
                    'booking_id' => $booking->id,
                    'error' => $e->getMessage(),
                ]);
                return back()->with('warning', 'Zahlungsstatus aktualisiert, aber E-Mail konnte nicht versendet werden: ' . $e->getMessage());
            }
        }

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

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $bookings = $query->get();

        $format = $request->get('format', 'csv'); // csv oder excel

        if ($format === 'excel') {
            return $this->exportExcel($bookings);
        }

        return $this->exportCsv($bookings);
    }

    private function exportCsv($bookings)
    {
        $filename = 'teilnehmerliste-' . now()->format('Y-m-d-His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $callback = function() use ($bookings) {
            $file = fopen('php://output', 'w');

            // UTF-8 BOM für korrekte Darstellung in Excel
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

            // Header
            fputcsv($file, [
                'Buchungsnummer',
                'Event-Titel',
                'Event-Datum',
                'Vorname',
                'Nachname',
                'Email',
                'Telefon',
                'Ticket-Typ',
                'Anzahl',
                'Preis',
                'Gesamt',
                'Status',
                'Zahlungsstatus',
                'Eingecheckt',
                'Buchungsdatum',
                'Adresse',
                'PLZ',
                'Stadt',
                'Land',
            ], ';');

            // Daten - für jedes Ticket eine Zeile
            foreach ($bookings as $booking) {
                $nameParts = explode(' ', $booking->customer_name, 2);
                $firstName = $nameParts[0] ?? '';
                $lastName = $nameParts[1] ?? '';

                foreach ($booking->items as $item) {
                    fputcsv($file, [
                        $booking->booking_number,
                        $booking->event->title,
                        $booking->event->start_date->format('d.m.Y H:i'),
                        $firstName,
                        $lastName,
                        $booking->customer_email,
                        $booking->customer_phone ?? '',
                        $item->ticketType->name ?? 'Standard',
                        $item->quantity,
                        number_format($item->price, 2, ',', '.'),
                        number_format($booking->total_amount, 2, ',', '.'),
                        $booking->status,
                        $booking->payment_status,
                        $item->checked_in_at ? 'Ja' : 'Nein',
                        $booking->created_at->format('d.m.Y H:i'),
                        $booking->billing_address ?? '',
                        $booking->billing_postal_code ?? '',
                        $booking->billing_city ?? '',
                        $booking->billing_country ?? '',
                    ], ';');
                }
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function exportExcel($bookings)
    {
        // Für eine einfache Excel-Export-Variante ohne Package
        $filename = 'teilnehmerliste-' . now()->format('Y-m-d-His') . '.xls';

        $headers = [
            'Content-Type' => 'application/vnd.ms-excel; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $content = '<html><head><meta charset="UTF-8"></head><body>';
        $content .= '<table border="1">';

        // Header
        $content .= '<tr>';
        $content .= '<th>Buchungsnummer</th>';
        $content .= '<th>Event-Titel</th>';
        $content .= '<th>Event-Datum</th>';
        $content .= '<th>Vorname</th>';
        $content .= '<th>Nachname</th>';
        $content .= '<th>Email</th>';
        $content .= '<th>Telefon</th>';
        $content .= '<th>Ticket-Typ</th>';
        $content .= '<th>Anzahl</th>';
        $content .= '<th>Preis</th>';
        $content .= '<th>Gesamt</th>';
        $content .= '<th>Status</th>';
        $content .= '<th>Zahlungsstatus</th>';
        $content .= '<th>Eingecheckt</th>';
        $content .= '<th>Buchungsdatum</th>';
        $content .= '<th>Adresse</th>';
        $content .= '<th>PLZ</th>';
        $content .= '<th>Stadt</th>';
        $content .= '<th>Land</th>';
        $content .= '</tr>';

        // Daten
        foreach ($bookings as $booking) {
            $nameParts = explode(' ', $booking->customer_name, 2);
            $firstName = $nameParts[0] ?? '';
            $lastName = $nameParts[1] ?? '';

            foreach ($booking->items as $item) {
                $content .= '<tr>';
                $content .= '<td>' . htmlspecialchars($booking->booking_number) . '</td>';
                $content .= '<td>' . htmlspecialchars($booking->event->title) . '</td>';
                $content .= '<td>' . $booking->event->start_date->format('d.m.Y H:i') . '</td>';
                $content .= '<td>' . htmlspecialchars($firstName) . '</td>';
                $content .= '<td>' . htmlspecialchars($lastName) . '</td>';
                $content .= '<td>' . htmlspecialchars($booking->customer_email) . '</td>';
                $content .= '<td>' . htmlspecialchars($booking->customer_phone ?? '') . '</td>';
                $content .= '<td>' . htmlspecialchars($item->ticketType->name ?? 'Standard') . '</td>';
                $content .= '<td>' . $item->quantity . '</td>';
                $content .= '<td>' . number_format($item->price, 2, ',', '.') . ' €</td>';
                $content .= '<td>' . number_format($booking->total_amount, 2, ',', '.') . ' €</td>';
                $content .= '<td>' . htmlspecialchars($booking->status) . '</td>';
                $content .= '<td>' . htmlspecialchars($booking->payment_status) . '</td>';
                $content .= '<td>' . ($item->checked_in_at ? 'Ja' : 'Nein') . '</td>';
                $content .= '<td>' . $booking->created_at->format('d.m.Y H:i') . '</td>';
                $content .= '<td>' . htmlspecialchars($booking->billing_address ?? '') . '</td>';
                $content .= '<td>' . htmlspecialchars($booking->billing_postal_code ?? '') . '</td>';
                $content .= '<td>' . htmlspecialchars($booking->billing_city ?? '') . '</td>';
                $content .= '<td>' . htmlspecialchars($booking->billing_country ?? '') . '</td>';
                $content .= '</tr>';
            }
        }

        $content .= '</table></body></html>';

        return response($content, 200, $headers);
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

