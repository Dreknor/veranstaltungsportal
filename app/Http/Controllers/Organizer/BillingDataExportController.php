<?php

namespace App\Http\Controllers\Organizer;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class BillingDataExportController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth']);
    }

    /**
     * Übersicht der Rechnungsdaten für externe Fakturierung
     */
    public function index(Request $request)
    {
        $organization = auth()->user()->currentOrganization();
        if (!$organization) {
            return redirect()->route('organizer.organizations.select');
        }

        $query = Booking::whereHas('event', fn ($q) => $q->where('organization_id', $organization->id))
            ->with(['event', 'items.ticketType'])
            ->where('status', '!=', 'cancelled')
            ->where('total', '>', 0);

        // Filter: fakturiert / nicht fakturiert
        if ($request->filter === 'invoiced') {
            $query->where('externally_invoiced', true);
        } elseif ($request->filter === 'pending') {
            $query->where('externally_invoiced', false);
        }

        // Suche
        if ($request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('booking_number', 'like', "%{$search}%")
                  ->orWhere('customer_name', 'like', "%{$search}%")
                  ->orWhere('customer_email', 'like', "%{$search}%")
                  ->orWhere('external_invoice_number', 'like', "%{$search}%");
            });
        }

        $bookings = $query->latest()->paginate(25)->withQueryString();

        return view('organizer.billing-data.index', compact('organization', 'bookings'));
    }

    /**
     * Excel/CSV-Export der Rechnungsdaten
     */
    public function export(Request $request)
    {
        $organization = auth()->user()->currentOrganization();
        if (!$organization) {
            return redirect()->route('organizer.organizations.select');
        }

        $query = Booking::whereHas('event', fn ($q) => $q->where('organization_id', $organization->id))
            ->with(['event', 'items.ticketType'])
            ->where('status', '!=', 'cancelled')
            ->where('total', '>', 0);

        if ($request->filter === 'invoiced') {
            $query->where('externally_invoiced', true);
        } elseif ($request->filter === 'pending') {
            $query->where('externally_invoiced', false);
        }

        $bookings = $query->latest()->get();

        $filename = 'Rechnungsdaten_' . $organization->slug . '_' . now()->format('Y-m-d') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($bookings) {
            $handle = fopen('php://output', 'w');
            // BOM für Excel UTF-8
            fwrite($handle, "\xEF\xBB\xBF");

            fputcsv($handle, [
                'Buchungsnummer',
                'Veranstaltung',
                'Datum',
                'Kundenname',
                'Kundenemail',
                'Organisation/Einrichtung',
                'Firma (Rechnung)',
                'USt-IdNr.',
                'Rechnungsadresse',
                'PLZ',
                'Stadt',
                'Land',
                'Nettobetrag',
                'Bruttobetrag',
                'MwSt.-Satz',
                'Zahlungsmethode',
                'Status',
                'Zahlungsstatus',
                'Buchungsdatum',
                'Externe Rechnungsnummer',
                'Fakturiert am',
            ], ';');

            foreach ($bookings as $booking) {
                $taxRate = config('monetization.tax_rate', 0);
                $net = $booking->total / (1 + $taxRate / 100);

                fputcsv($handle, [
                    $booking->booking_number,
                    $booking->event->title ?? '',
                    $booking->event->start_date?->format('d.m.Y') ?? '',
                    $booking->customer_name,
                    $booking->customer_email,
                    $booking->customer_organization ?? '',
                    $booking->billing_company ?? '',
                    $booking->billing_vat_id ?? '',
                    $booking->billing_address ?? '',
                    $booking->billing_postal_code ?? '',
                    $booking->billing_city ?? '',
                    $booking->billing_country ?? '',
                    number_format($net, 2, ',', '.'),
                    number_format($booking->total, 2, ',', '.'),
                    $taxRate . '%',
                    $booking->payment_method ?? '',
                    $booking->status,
                    $booking->payment_status,
                    $booking->created_at?->format('d.m.Y H:i') ?? '',
                    $booking->external_invoice_number ?? '',
                    $booking->externally_invoiced_at?->format('d.m.Y H:i') ?? '',
                ], ';');
            }

            fclose($handle);
        };

        return Response::stream($callback, 200, $headers);
    }

    /**
     * DATEV Buchungsstapel CSV-Export (EXTF-Format v700, Kategorie 21)
     *
     * Exportiert bezahlte Buchungen als DATEV-kompatible CSV-Datei.
     * Nur payment_status = 'paid' Buchungen werden exportiert.
     */
    public function exportDatev(Request $request)
    {
        $organization = auth()->user()->currentOrganization();
        if (!$organization) {
            return redirect()->route('organizer.organizations.select');
        }

        $bookings = Booking::whereHas('event', fn ($q) => $q->where('organization_id', $organization->id))
            ->where('total', '>', 0)
            ->where('payment_status', 'paid') // Nur bezahlte Buchungen für Buchhaltung
            ->with(['event', 'items.ticketType'])
            ->when($request->event_id, fn ($q, $id) => $q->where('event_id', $id))
            ->when($request->date_from, fn ($q, $d) => $q->where('created_at', '>=', $d))
            ->when($request->date_to, fn ($q, $d) => $q->where('created_at', '<=', $d . ' 23:59:59'))
            ->orderBy('created_at', 'asc')
            ->get();

        $filename = 'datev-buchungsstapel-' . now()->format('Y-m-d') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($bookings, $organization) {
            $file = fopen('php://output', 'w');

            // UTF-8 BOM (DATEV-Spezifikation verlangt BOM bei UTF-8)
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

            // DATEV EXTF-Header Zeile 1 (Pflichtformat)
            fputcsv($file, [
                '"EXTF"', 700, 21, 'Buchungsstapel', 12, '', '', '', '', '',
                'RE', '', '', '', '', 4, 0,
                mb_substr($organization->name ?? '', 0, 30), '', '',
            ], ';');

            // Spaltenüberschriften (Zeile 2 – nach DATEV-Spezifikation)
            fputcsv($file, [
                'Umsatz (ohne Soll/Haben-Kz)',
                'Soll/Haben-Kennzeichen',
                'WKZ Umsatz',
                'Kurs',
                'Basis-Umsatz',
                'WKZ Basis-Umsatz',
                'Konto',
                'Gegenkonto (ohne BU-Schlüssel)',
                'BU-Schlüssel',
                'Belegdatum',
                'Belegfeld 1',
                'Belegfeld 2',
                'Skonto',
                'Buchungstext',
                'Postensperre', 'Diverse Adressnummer', 'Geschäftspartnerbank',
                'Sachverhalt', 'Zinssperre', 'Beleglink',
            ], ';');

            // Datensätze
            foreach ($bookings as $booking) {
                // DATEV erwartet Komma als Dezimaltrenner, kein Tausendertrennzeichen
                $betrag = number_format((float) $booking->total, 2, ',', '');
                // Belegdatum: DDMM (ohne Jahr)
                $datum = $booking->created_at->format('dm');
                // Buchungstext: max. 60 Zeichen
                $text = mb_substr(
                    ($booking->event->title ?? '') . ' ' . $booking->customer_name,
                    0, 60
                );
                // Belegnummer: Buchungsnummer (max. 36 Zeichen)
                $belegnr = mb_substr($booking->booking_number, 0, 36);

                fputcsv($file, [
                    $betrag,        // Umsatz
                    'S',            // Soll/Haben: S = Soll (Forderung an Debitor)
                    'EUR',          // WKZ Umsatz
                    '',             // Kurs
                    '',             // Basis-Umsatz
                    '',             // WKZ Basis-Umsatz
                    '8400',         // Erlöskonto 19% USt (SKR03/SKR04)
                    '10000',        // Debitorenkonto Sammelkonto
                    '',             // BU-Schlüssel
                    $datum,         // Belegdatum DDMM
                    $belegnr,       // Belegfeld 1
                    '',             // Belegfeld 2
                    '',             // Skonto
                    $text,          // Buchungstext
                    '', '', '', '', '', '',  // restliche Felder leer
                ], ';');
            }

            fclose($file);
        };

        return Response::stream($callback, 200, $headers);
    }

    /**
     * Buchung als (extern) fakturiert markieren
     */
    public function markAsInvoiced(Request $request, Booking $booking)
    {
        $organization = auth()->user()->currentOrganization();
        if (!$organization) {
            abort(403);
        }

        // Sicherstellen, dass Buchung zur Organisation gehört
        if ($booking->event->organization_id !== $organization->id) {
            abort(403);
        }

        $request->validate([
            'external_invoice_number' => 'nullable|string|max:100',
        ]);

        $booking->update([
            'externally_invoiced' => true,
            'externally_invoiced_at' => now(),
            'external_invoice_number' => $request->external_invoice_number,
        ]);

        return back()->with('status', "Buchung {$booking->booking_number} wurde als fakturiert markiert.");
    }

    /**
     * Mehrere Buchungen als fakturiert markieren
     */
    public function bulkMarkAsInvoiced(Request $request)
    {
        $organization = auth()->user()->currentOrganization();
        if (!$organization) {
            abort(403);
        }

        $request->validate([
            'booking_ids' => 'required|array',
            'booking_ids.*' => 'integer|exists:bookings,id',
        ]);

        $updated = Booking::whereIn('id', $request->booking_ids)
            ->whereHas('event', fn ($q) => $q->where('organization_id', $organization->id))
            ->update([
                'externally_invoiced' => true,
                'externally_invoiced_at' => now(),
            ]);

        return back()->with('status', "{$updated} Buchung(en) wurden als fakturiert markiert.");
    }
}

