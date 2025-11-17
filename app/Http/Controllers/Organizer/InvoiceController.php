<?php

namespace App\Http\Controllers\Organizer;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\PlatformFee;
use App\Services\InvoiceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class InvoiceController extends Controller
{
    protected InvoiceService $invoiceService;

    public function __construct(InvoiceService $invoiceService)
    {
        $this->invoiceService = $invoiceService;
    }

    /**
     * Display a listing of invoices
     */
    public function index(Request $request)
    {
        $organization = auth()->user()->currentOrganization();
        if (!$organization) {
            return redirect()->route('organizer.organizations.select');
        }

        $query = Invoice::whereHas('event', function ($q) use ($organization) {
                $q->where('organization_id', $organization->id);
            })
            ->where('type', 'platform_fee')
            ->with('event')
            ->orderBy('invoice_date', 'desc');

        // Filter by status
        if ($request->has('status') && $request->status !== '') {
            $query->where('status', $request->status);
        }

        // Filter by date range
        if ($request->has('from_date') && $request->from_date) {
            $query->whereDate('invoice_date', '>=', $request->from_date);
        }
        if ($request->has('to_date') && $request->to_date) {
            $query->whereDate('invoice_date', '<=', $request->to_date);
        }

        $invoices = $query->paginate(15);

        // Calculate totals
        $totals = [
            'total' => Invoice::whereHas('event', function ($q) use ($organization) {
                $q->where('organization_id', $organization->id);
            })->where('type', 'platform_fee')->sum('total_amount'),
            'paid' => Invoice::whereHas('event', function ($q) use ($organization) {
                $q->where('organization_id', $organization->id);
            })->where('type', 'platform_fee')->where('status', 'paid')->sum('total_amount'),
            'pending' => Invoice::whereHas('event', function ($q) use ($organization) {
                $q->where('organization_id', $organization->id);
            })->where('type', 'platform_fee')->whereIn('status', ['sent', 'overdue'])->sum('total_amount'),
        ];

        return view('organizer.invoices.index', compact('invoices', 'totals', 'organization'));
    }

    /**
     * Display the specified invoice
     */
    public function show(Invoice $invoice)
    {
        // Authorization: invoice must belong to current org
        $organization = auth()->user()->currentOrganization();
        if (!$organization || !$invoice->event || $invoice->event->organization_id !== $organization->id) {
            abort(403, 'Unberechtigt');
        }

        $invoice->load('event');

        // Get platform fees for this invoice
        $platformFees = PlatformFee::where('event_id', $invoice->event_id)->get();

        return view('organizer.invoices.show', compact('invoice', 'platformFees', 'organization'));
    }

    /**
     * Download invoice PDF
     */
    public function download(Invoice $invoice)
    {
        $organization = auth()->user()->currentOrganization();
        if (!$organization || !$invoice->event || $invoice->event->organization_id !== $organization->id) {
            abort(403, 'Unberechtigt');
        }

        if (!$invoice->pdf_path || !file_exists(storage_path("app/{$invoice->pdf_path}"))) {
            return back()->with('error', 'PDF nicht gefunden.');
        }

        return response()->download(
            storage_path("app/{$invoice->pdf_path}"),
            "Rechnung_{$invoice->invoice_number}.pdf"
        );
    }

    /**
     * Mark invoice as paid (admin only, but prepared for future use)
     */
    public function markAsPaid(Request $request, Invoice $invoice)
    {
        // Authorization - only admin or system can mark as paid
        if (!auth()->user()->hasRole('admin')) {
            abort(403, 'Nur Administratoren können Rechnungen als bezahlt markieren.');
        }

        $invoice->update([
            'status' => 'paid',
            'paid_at' => now(),
        ]);

        Log::info('Invoice marked as paid', [
            'invoice_id' => $invoice->id,
            'invoice_number' => $invoice->invoice_number,
            'marked_by' => auth()->id(),
        ]);

        return back()->with('success', 'Rechnung wurde als bezahlt markiert.');
    }

    /**
     * Overview of platform fees
     */
    public function platformFees(Request $request)
    {
        $organization = auth()->user()->currentOrganization();
        if (!$organization) {
            return redirect()->route('organizer.organizations.select');
        }

        $query = PlatformFee::whereHas('event', function ($q) use ($organization) {
            $q->where('organization_id', $organization->id);
        })->with(['event', 'booking']);

        // Filter by event
        if ($request->has('event_id') && $request->event_id) {
            $query->where('event_id', $request->event_id);
        }

        // Filter by date range
        if ($request->has('from_date') && $request->from_date) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }
        if ($request->has('to_date') && $request->to_date) {
            $query->whereDate('created_at', '<=', $request->to_date);
        }

        $platformFees = $query->orderBy('created_at', 'desc')->paginate(20);

        // Get user's events for filter dropdown
        $events = $organization->events()->orderBy('start_date', 'desc')->get();

        // Calculate totals
        $totalFees = PlatformFee::whereHas('event', function ($q) use ($organization) {
            $q->where('organization_id', $organization->id);
        })->sum('fee_amount');

        $totalBookings = PlatformFee::whereHas('event', function ($q) use ($organization) {
            $q->where('organization_id', $organization->id);
        })->sum('booking_amount');

        return view('organizer.invoices.platform-fees', compact(
            'platformFees', 'events', 'totalFees', 'totalBookings', 'organization'
        ));
    }

    /**
     * Export invoices
     */
    public function export(Request $request)
    {
        $organization = auth()->user()->currentOrganization();
        if (!$organization) {
            return back()->with('error', 'Keine Organisation ausgewählt.');
        }

        $invoices = Invoice::whereHas('event', function ($q) use ($organization) {
                $q->where('organization_id', $organization->id);
            })
            ->where('type', 'platform_fee')
            ->with('event')
            ->orderBy('invoice_date', 'desc')
            ->get();

        $format = $request->get('format', 'csv');
        if ($format === 'csv') {
            return $this->exportCsv($invoices);
        }
        return back()->with('error', 'Ungültiges Export-Format.');
    }

    /**
     * Export invoices as CSV
     */
    private function exportCsv($invoices)
    {
        $filename = 'rechnungen_' . now()->format('Y-m-d') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($invoices) {
            $file = fopen('php://output', 'w');

            // UTF-8 BOM for Excel
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));

            // Header
            fputcsv($file, [
                'Rechnungsnummer', 'Datum', 'Fälligkeitsdatum', 'Event', 'Betrag (netto)', 'MwSt.', 'Gesamt (brutto)', 'Status', 'Bezahlt am',
            ], ';');

            // Data
            foreach ($invoices as $invoice) {
                fputcsv($file, [
                    $invoice->invoice_number,
                    $invoice->invoice_date->format('d.m.Y'),
                    $invoice->due_date->format('d.m.Y'),
                    $invoice->event->title ?? 'N/A',
                    number_format($invoice->amount, 2, ',', '.'),
                    number_format($invoice->tax_amount, 2, ',', '.'),
                    number_format($invoice->total_amount, 2, ',', '.'),
                    $invoice->status,
                    $invoice->paid_at ? $invoice->paid_at->format('d.m.Y') : '',
                ], ';');
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}

