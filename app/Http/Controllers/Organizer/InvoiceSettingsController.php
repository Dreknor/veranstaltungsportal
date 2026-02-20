<?php

namespace App\Http\Controllers\Organizer;

use App\Http\Controllers\Controller;
use App\Services\InvoiceNumberService;
use App\Services\TicketPdfService;
use Illuminate\Http\Request;

class InvoiceSettingsController extends Controller
{
    public function __construct(
        protected InvoiceNumberService $invoiceNumberService,
        protected TicketPdfService $ticketPdfService,
    ) {
        $this->middleware(['auth']);
    }

    /**
     * Show invoice settings form
     */
    public function index()
    {
        $organization = auth()->user()->currentOrganization();
        if (!$organization) {
            return redirect()->route('organizer.organizations.select');
        }

        $settings = $organization->invoice_settings ?? [];

        // Default values
        $defaults = [
            'invoice_number_format_booking' => 'RE-{YEAR}-{NUMBER}',
            'invoice_number_padding' => 5,
            'invoice_reset_yearly' => true,
        ];

        $settings = array_merge($defaults, $settings);
        $settings['invoice_number_counter_booking'] = $organization->invoice_counter_booking ?? 1;

        $placeholders = $this->invoiceNumberService->getAvailablePlaceholders();

        return view('organizer.settings.invoice', compact('settings', 'placeholders', 'organization'));
    }

    /**
     * Update invoice settings
     */
    public function update(Request $request)
    {
        $organization = auth()->user()->currentOrganization();
        if (!$organization) {
            return redirect()->route('organizer.organizations.select');
        }

        $request->validate([
            'invoice_number_format_booking' => 'required|string|max:100',
            'invoice_number_counter_booking' => 'required|integer|min:1',
            'invoice_number_padding' => 'required|integer|min:1|max:10',
            'invoice_reset_yearly' => 'required|boolean',
        ]);

        // Validate format string
        if (!$this->invoiceNumberService->validateFormat($request->invoice_number_format_booking)) {
            return back()->withErrors([
                'invoice_number_format_booking' => 'Ungültiges Format für Buchungs-Rechnungsnummern. Es muss {NUMBER} oder {COUNTER} enthalten.'
            ])->withInput();
        }

        // Save settings
        $invoiceSettings = [
            'invoice_number_format_booking' => $request->invoice_number_format_booking,
            'invoice_number_padding' => $request->invoice_number_padding,
            'invoice_reset_yearly' => $request->invoice_reset_yearly,
        ];

        $organization->update([
            'invoice_settings' => $invoiceSettings,
            'invoice_counter_booking' => $request->invoice_number_counter_booking,
            'invoice_counter_booking_year' => now()->year,
        ]);

        return back()->with('status', 'Rechnungsnummern-Einstellungen wurden erfolgreich aktualisiert.');
    }

    /**
     * Preview invoice number format
     */
    public function preview(Request $request)
    {
        $format = $request->input('format', 'RE-{YEAR}-{NUMBER}');
        $padding = $request->input('padding', 5);

        if (!$this->invoiceNumberService->validateFormat($format)) {
            return response()->json([
                'success' => false,
                'message' => 'Ungültiges Format. Es muss {NUMBER} oder {COUNTER} enthalten.'
            ], 400);
        }

        $preview = $this->invoiceNumberService->previewFormat($format, $padding);

        return response()->json([
            'success' => true,
            'preview' => $preview
        ]);
    }

    /**
     * Download sample invoice as PDF without incrementing counter.
     * Uses the exact same template as real ticket invoices.
     */
    public function sampleInvoice()
    {
        $organization = auth()->user()->currentOrganization();
        if (!$organization) {
            return redirect()->route('organizer.organizations.select');
        }

        $invoiceNumber = $this->invoiceNumberService->previewNextBookingInvoiceNumber(auth()->user());

        $pdf = $this->ticketPdfService->generateSampleInvoice($organization, $invoiceNumber);

        return $pdf->download('Beispielrechnung_' . $invoiceNumber . '.pdf');
    }
}
