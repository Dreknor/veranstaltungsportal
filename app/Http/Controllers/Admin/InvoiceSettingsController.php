<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Services\InvoiceNumberService;
use Illuminate\Http\Request;

class InvoiceSettingsController extends Controller
{
    protected InvoiceNumberService $invoiceNumberService;

    public function __construct(InvoiceNumberService $invoiceNumberService)
    {
        $this->middleware(['auth', 'role:admin']);
        $this->invoiceNumberService = $invoiceNumberService;
    }

    /**
     * Show invoice settings form
     */
    public function index()
    {
        $settings = [
            'invoice_number_format_booking' => Setting::getValue('invoice_number_format_booking', 'RE-{YEAR}-{NUMBER}'),
            'invoice_number_format_platform_fee' => Setting::getValue('invoice_number_format_platform_fee', 'PF-{YEAR}-{NUMBER}'),
            'invoice_number_counter_booking' => Setting::getValue('invoice_number_counter_booking', 1),
            'invoice_number_counter_platform_fee' => Setting::getValue('invoice_number_counter_platform_fee', 1),
            'invoice_number_padding' => Setting::getValue('invoice_number_padding', 5),
            'invoice_reset_yearly' => Setting::getValue('invoice_reset_yearly', true),
        ];

        $placeholders = $this->invoiceNumberService->getAvailablePlaceholders();

        return view('admin.settings.invoice', compact('settings', 'placeholders'));
    }

    /**
     * Update invoice settings
     */
    public function update(Request $request)
    {
        $request->validate([
            'invoice_number_format_booking' => 'required|string|max:100',
            'invoice_number_format_platform_fee' => 'required|string|max:100',
            'invoice_number_counter_booking' => 'required|integer|min:1',
            'invoice_number_counter_platform_fee' => 'required|integer|min:1',
            'invoice_number_padding' => 'required|integer|min:1|max:10',
            'invoice_reset_yearly' => 'required|boolean',
        ]);

        // Validate format strings
        if (!$this->invoiceNumberService->validateFormat($request->invoice_number_format_booking)) {
            return back()->withErrors([
                'invoice_number_format_booking' => 'Ungültiges Format für Buchungs-Rechnungsnummern. Es muss {NUMBER} oder {COUNTER} enthalten.'
            ])->withInput();
        }

        if (!$this->invoiceNumberService->validateFormat($request->invoice_number_format_platform_fee)) {
            return back()->withErrors([
                'invoice_number_format_platform_fee' => 'Ungültiges Format für Plattformgebühren-Rechnungsnummern. Es muss {NUMBER} oder {COUNTER} enthalten.'
            ])->withInput();
        }

        // Save settings
        Setting::setValue('invoice_number_format_booking', $request->invoice_number_format_booking);
        Setting::setValue('invoice_number_format_platform_fee', $request->invoice_number_format_platform_fee);
        Setting::setValue('invoice_number_counter_booking', $request->invoice_number_counter_booking);
        Setting::setValue('invoice_number_counter_platform_fee', $request->invoice_number_counter_platform_fee);
        Setting::setValue('invoice_number_padding', $request->invoice_number_padding);
        Setting::setValue('invoice_reset_yearly', $request->invoice_reset_yearly);

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
}

