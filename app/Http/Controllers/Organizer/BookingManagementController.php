<?php

namespace App\Http\Controllers\Organizer;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Event;
use App\Services\BookingWorkflowService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class BookingManagementController extends Controller
{
    public function index(Request $request)
    {
        $organization = auth()->user()->currentOrganization();
        if (!$organization) {
            return redirect()->route('organizer.organizations.select');
        }

        $isArchive = $request->boolean('archive');

        $query = Booking::whereHas('event', function ($q) use ($organization, $isArchive) {
            $q->where('organization_id', $organization->id);
            if ($isArchive) {
                $q->where('end_date', '<', now());
            } else {
                $q->where('end_date', '>=', now());
            }
        })->with(['event', 'items']);

        // Filter nach Event
        $filterByEvent = $request->filled('event_id');
        if ($filterByEvent) {
            $query->where('event_id', $request->event_id);
        }

        // Filter nach Status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter nach Zahlungsstatus
        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        // Suche
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('booking_number', 'LIKE', "%{$search}%")
                    ->orWhere('customer_name', 'LIKE', "%{$search}%")
                    ->orWhere('customer_email', 'LIKE', "%{$search}%");
            });
        }

        // Events für Dropdown (gefiltert nach Archiv-Status)
        $events = $organization->events()
            ->when($isArchive, fn($q) => $q->where('end_date', '<', now()))
            ->when(!$isArchive, fn($q) => $q->where('end_date', '>=', now()))
            ->orderBy('start_date', 'desc')
            ->get();

        // Zähler für Tab-Badges
        $upcomingBookingsCount = Booking::whereHas('event', function ($q) use ($organization) {
            $q->where('organization_id', $organization->id)->where('end_date', '>=', now());
        })->count();
        $archiveBookingsCount = Booking::whereHas('event', function ($q) use ($organization) {
            $q->where('organization_id', $organization->id)->where('end_date', '<', now());
        })->count();

        // Handlungsbedarf (nur kommende Veranstaltungen)
        $upcomingScope = fn ($q) => $q->where('organization_id', $organization->id)->where('end_date', '>=', now());
        $pendingApprovalCount = Booking::whereHas('event', $upcomingScope)->where('status', 'pending_approval')->count();
        $unpaidCount = Booking::whereHas('event', $upcomingScope)
            ->where('status', 'pending')
            ->where('payment_status', 'pending')
            ->where('total', '>', 0)
            ->count();

        // Gruppierte Ansicht (kein Event-Filter): Events mit Buchungen laden
        $groupByEvent = !$filterByEvent
            && !$request->filled('status')
            && !$request->filled('payment_status')
            && !$request->filled('search');

        if ($groupByEvent) {
            $groupedEventsQuery = $organization->events()
                ->withCount(['bookings', 'bookings as confirmed_bookings_count' => function ($q) {
                    $q->where('status', 'confirmed');
                }, 'bookings as pending_bookings_count' => function ($q) {
                    $q->whereIn('status', ['pending', 'pending_approval']);
                }])
                ->has('bookings')
                ->with(['bookings' => function ($q) {
                    $q->with('items')->latest();
                }]);

            if ($isArchive) {
                $groupedEventsQuery->where('end_date', '<', now());
            } else {
                $groupedEventsQuery->where('end_date', '>=', now());
            }

            $groupedEvents = $groupedEventsQuery
                ->orderBy('start_date', $isArchive ? 'desc' : 'asc')
                ->paginate(10);

            return view('organizer.bookings.index', compact(
                'groupedEvents', 'events', 'organization', 'groupByEvent',
                'isArchive', 'upcomingBookingsCount', 'archiveBookingsCount',
                'pendingApprovalCount', 'unpaidCount'
            ));
        }

        $bookings = $query->latest()->paginate(20);
        return view('organizer.bookings.index', compact(
            'bookings', 'events', 'organization', 'groupByEvent',
            'isArchive', 'upcomingBookingsCount', 'archiveBookingsCount',
            'pendingApprovalCount', 'unpaidCount'
        ));
    }

    public function show(Booking $booking)
    {
        $this->authorize('view', $booking);
        $booking->load(['event.organization', 'items.ticketType', 'user', 'discountCode', 'emailLogs']);
        return view('organizer.bookings.show', compact('booking'));
    }

    /**
     * Buchungsstatus manuell setzen.
     *
     * Kommunikationsregeln:
     *  - Freigabe (pending_approval → confirmed): Bestätigung mit Tickets/Zugangsdaten
     *  - Bestätigen einer bereits bezahlten Buchung: Tickets/Zugangsdaten
     *  - Stornieren: Stornobestätigung an den Kunden
     *  - alle übrigen Wechsel erfolgen ohne E-Mail
     */
    public function updateStatus(Request $request, Booking $booking, BookingWorkflowService $workflow)
    {
        $this->authorize('update', $booking);
        $request->validate([
            'status' => 'required|in:pending,pending_approval,confirmed,cancelled,completed',
        ]);

        $newStatus = $request->status;

        if ($newStatus === $booking->status) {
            return back()->with('info', 'Der Status ist bereits gesetzt.');
        }

        if ($newStatus === 'cancelled') {
            $workflow->cancel($booking, 'organizer');
            return back()->with('success', 'Buchung storniert. Der Kunde hat eine Stornobestätigung erhalten.');
        }

        if ($booking->status === 'cancelled') {
            return back()->with('error', 'Stornierte Buchungen können nicht reaktiviert werden. Bitte legen Sie eine neue Buchung an.');
        }

        if ($newStatus === 'confirmed' && $booking->status === 'pending_approval') {
            $workflow->approve($booking);
            return back()->with('success', 'Anmeldung bestätigt. Die Bestätigung mit Tickets bzw. Zugangsdaten wurde per E-Mail versendet.');
        }

        $booking->update([
            'status' => $newStatus,
            'confirmed_at' => $newStatus === 'confirmed' ? ($booking->confirmed_at ?? now()) : $booking->confirmed_at,
        ]);

        if ($newStatus === 'confirmed' && $workflow->deliverTickets($booking)) {
            return back()->with('success', 'Buchung bestätigt. Tickets bzw. Zugangsdaten wurden per E-Mail versendet.');
        }

        if ($newStatus === 'confirmed') {
            return back()->with('success', 'Buchung bestätigt. Tickets bzw. Zugangsdaten werden automatisch versendet, sobald die Zahlung verbucht ist.');
        }

        return back()->with('success', 'Buchungsstatus aktualisiert (ohne E-Mail an den Kunden).');
    }

    /**
     * Zahlungsstatus setzen. "Bezahlt" bzw. "Extern fakturiert" bestätigen die Buchung und
     * versenden automatisch Tickets/Zugangsdaten.
     */
    public function updatePaymentStatus(Request $request, Booking $booking, BookingWorkflowService $workflow)
    {
        $this->authorize('update', $booking);
        $request->validate([
            'payment_status' => 'required|in:pending,paid,extern,refunded,failed',
        ]);

        if ($booking->status === 'cancelled' && in_array($request->payment_status, ['paid', 'extern'])) {
            return back()->with('error', 'Für stornierte Buchungen kann keine Zahlung verbucht werden.');
        }

        if ($request->payment_status === $booking->payment_status) {
            return back()->with('info', 'Der Zahlungsstatus ist bereits gesetzt.');
        }

        $message = $workflow->changePaymentStatus($booking, $request->payment_status);

        return back()->with('success', $message);
    }

    /**
     * Die zum aktuellen Stand passende E-Mail erneut an den Kunden senden
     * (z. B. wenn Tickets oder Zugangsdaten nicht angekommen sind).
     */
    public function resend(Booking $booking, BookingWorkflowService $workflow)
    {
        $this->authorize('update', $booking);

        $sent = $workflow->resendCurrentState($booking);

        if (!$sent) {
            return back()->with('error', 'Für diese Buchung kann keine E-Mail erneut versendet werden.');
        }

        return back()->with('success', "„{$sent}“ wurde erneut an {$booking->customer_email} gesendet.");
    }

    /**
     * Tickets/Zugangsdaten für eine einzelne Buchung vor Zahlung bzw. Rechnungsstellung freigeben
     * (z. B. Rechnung an den Schulträger nach der Veranstaltung).
     */
    public function releaseTickets(Booking $booking, BookingWorkflowService $workflow)
    {
        $this->authorize('update', $booking);

        if ($booking->payment_method === 'paypal') {
            return back()->with('error', 'PayPal-Buchungen werden erst nach erfolgter Zahlung freigegeben.');
        }

        if (!$workflow->releaseTicketsBeforePayment($booking)) {
            return back()->with('error', 'Für diese Buchung ist keine Vorab-Freigabe möglich (bereits freigegeben, storniert oder kostenfrei).');
        }

        return back()->with('success', 'Buchung bestätigt und Tickets bzw. Zugangsdaten versendet. Die Rechnung ist weiterhin offen.');
    }

    /**
     * Buchung durch den Veranstalter stornieren (mit optionalem Grund).
     */
    public function cancel(Request $request, Booking $booking, BookingWorkflowService $workflow)
    {
        $this->authorize('update', $booking);

        $validated = $request->validate([
            'cancellation_reason' => 'nullable|string|max:1000',
            'notify_customer' => 'nullable|boolean',
        ]);

        $notify = $request->boolean('notify_customer', true);

        if (!$workflow->cancel($booking, 'organizer', $validated['cancellation_reason'] ?? null, $notify)) {
            return back()->with('error', 'Diese Buchung kann nicht storniert werden.');
        }

        return back()->with('success', $notify
            ? 'Buchung storniert. Der Kunde hat eine Stornobestätigung erhalten.'
            : 'Buchung storniert (ohne E-Mail an den Kunden).');
    }

    public function export(Request $request)
    {
        $organization = auth()->user()->currentOrganization();
        if (!$organization) {
            return redirect()->route('organizer.organizations.select');
        }

        $query = Booking::whereHas('event', function ($q) use ($organization) {
            $q->where('organization_id', $organization->id);
        })->with(['event', 'items.ticketType']);

        if ($request->filled('event_id')) {
            $query->where('event_id', $request->event_id);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        $bookings = $query->get();

        $format = $request->get('format', 'csv');
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
                'Organisation',
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
                'Firma (Rechnung)',
                'USt-IdNr.',
                'Extern fakturiert',
                'Externe Rechnungsnr.',
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
                        $item->attendee_organization ?? $booking->customer_organization ?? '',
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
                        $booking->billing_company ?? '',
                        $booking->billing_vat_id ?? '',
                        $booking->externally_invoiced ? 'Ja' : 'Nein',
                        $booking->external_invoice_number ?? '',
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
        $content .= '<th>Organisation</th>';
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
        $content .= '<th>Firma (Rechnung)</th>';
        $content .= '<th>USt-IdNr.</th>';
        $content .= '<th>Extern fakturiert</th>';
        $content .= '<th>Externe Rechnungsnr.</th>';
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
                $content .= '<td>' . htmlspecialchars($item->attendee_organization ?? $booking->customer_organization ?? '') . '</td>';
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
                $content .= '<td>' . htmlspecialchars($booking->billing_company ?? '') . '</td>';
                $content .= '<td>' . htmlspecialchars($booking->billing_vat_id ?? '') . '</td>';
                $content .= '<td>' . ($booking->externally_invoiced ? 'Ja' : 'Nein') . '</td>';
                $content .= '<td>' . htmlspecialchars($booking->external_invoice_number ?? '') . '</td>';
                $content .= '</tr>';
            }
        }

        $content .= '</table></body></html>';

        return response($content, 200, $headers);
    }

    /**
     * Kostenfreie Buchung manuell bestätigen (Veranstalter-Freigabe)
     */
    public function approveBooking(Booking $booking, BookingWorkflowService $workflow)
    {
        $this->authorize('update', $booking);

        if ($booking->status !== 'pending_approval') {
            return back()->with('error', 'Diese Buchung kann nicht bestätigt werden (Status: ' . $booking->statusLabel() . ').');
        }

        $workflow->approve($booking);

        return back()->with('success', 'Buchung bestätigt! Die Bestätigung mit Zugangsdaten bzw. Tickets wurde per E-Mail versendet.');
    }

    /**
     * Kostenfreie Buchung ablehnen
     */
    public function rejectBooking(Request $request, Booking $booking, BookingWorkflowService $workflow)
    {
        $this->authorize('update', $booking);

        if ($booking->status !== 'pending_approval') {
            return back()->with('error', 'Diese Buchung kann nicht abgelehnt werden.');
        }

        $request->validate([
            'rejection_reason' => 'nullable|string|max:500',
        ]);

        $workflow->reject($booking, $request->rejection_reason);

        return back()->with('success', 'Buchung abgelehnt. Der Teilnehmer wurde benachrichtigt.');
    }

    /**
     * Alle ausstehenden Buchungen eines Events bestätigen (Bulk-Aktion)
     */
    public function approveAllPending(Event $event, BookingWorkflowService $workflow)
    {
        $this->authorize('update', $event);

        $pendingBookings = $event->bookings()
            ->where('status', 'pending_approval')
            ->get();

        // Sammelfreigabe: Bestätigungen laufen über die Queue
        foreach ($pendingBookings as $booking) {
            $workflow->approve($booking, queue: true);
        }

        return back()->with('success', $pendingBookings->count() . ' Buchung(en) bestätigt und Bestätigungen versendet.');
    }

    public function checkIn(Request $request, Booking $booking)    {
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

