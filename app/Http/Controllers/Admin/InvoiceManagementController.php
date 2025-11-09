<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\PlatformFee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InvoiceManagementController extends Controller
{
    /**
     * Display a listing of all invoices
     */
    public function index(Request $request)
    {
        $query = Invoice::with(['event', 'user'])
            ->orderBy('invoice_date', 'desc');

        // Filter by type
        if ($request->has('type') && $request->type !== '') {
            $query->where('type', $request->type);
        }

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

        // Search
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('invoice_number', 'like', "%{$search}%")
                  ->orWhere('recipient_name', 'like', "%{$search}%")
                  ->orWhere('recipient_email', 'like', "%{$search}%");
            });
        }

        $invoices = $query->paginate(20);

        // Statistics
        $stats = [
            'total_count' => Invoice::count(),
            'total_amount' => Invoice::sum('total_amount'),
            'platform_fee_total' => Invoice::where('type', 'platform_fee')->sum('total_amount'),
            'participant_total' => Invoice::where('type', 'participant')->sum('total_amount'),
            'paid_count' => Invoice::where('status', 'paid')->count(),
            'pending_count' => Invoice::whereIn('status', ['sent', 'overdue'])->count(),
            'paid_amount' => Invoice::where('status', 'paid')->sum('total_amount'),
            'pending_amount' => Invoice::whereIn('status', ['sent', 'overdue'])->sum('total_amount'),
        ];

        return view('admin.invoices.index', compact('invoices', 'stats'));
    }

    /**
     * Display the specified invoice
     */
    public function show(Invoice $invoice)
    {
        $invoice->load('event', 'user', 'booking');

        // Get platform fees if platform_fee invoice
        $platformFees = null;
        if ($invoice->type === 'platform_fee' && $invoice->event_id) {
            $platformFees = PlatformFee::where('event_id', $invoice->event_id)->get();
        }

        return view('admin.invoices.show', compact('invoice', 'platformFees'));
    }

    /**
     * Download invoice PDF
     */
    public function download(Invoice $invoice)
    {
        if (!$invoice->pdf_path || !file_exists(storage_path("app/{$invoice->pdf_path}"))) {
            return back()->with('error', 'PDF nicht gefunden.');
        }

        return response()->download(
            storage_path("app/{$invoice->pdf_path}"),
            "Rechnung_{$invoice->invoice_number}.pdf"
        );
    }

    /**
     * Mark invoice as paid
     */
    public function markAsPaid(Request $request, Invoice $invoice)
    {
        $invoice->update([
            'status' => 'paid',
            'paid_at' => now(),
        ]);

        return back()->with('success', 'Rechnung wurde als bezahlt markiert.');
    }

    /**
     * Mark invoice as overdue
     */
    public function markAsOverdue(Invoice $invoice)
    {
        $invoice->update([
            'status' => 'overdue',
        ]);

        return back()->with('success', 'Rechnung wurde als überfällig markiert.');
    }

    /**
     * Resend invoice email
     */
    public function resend(Invoice $invoice)
    {
        try {
            // Use InvoiceService to send email
            $invoiceService = app(\App\Services\InvoiceService::class);

            // We need to call the private method via reflection or make it public
            // For now, we'll send directly
            \Mail::send('emails.invoice', compact('invoice'), function ($message) use ($invoice) {
                $message->to($invoice->recipient_email)
                        ->subject("Rechnung {$invoice->invoice_number}");

                $ccEmail = config('monetization.invoice_cc_email');
                if ($ccEmail) {
                    $message->cc($ccEmail);
                }

                if ($invoice->pdf_path && file_exists(storage_path("app/{$invoice->pdf_path}"))) {
                    $message->attach(storage_path("app/{$invoice->pdf_path}"));
                }
            });

            return back()->with('success', 'Rechnung wurde erneut per E-Mail versendet.');
        } catch (\Exception $e) {
            return back()->with('error', 'Fehler beim E-Mail-Versand: ' . $e->getMessage());
        }
    }

    /**
     * Export invoices
     */
    public function export(Request $request)
    {
        $query = Invoice::with(['event', 'user'])->orderBy('invoice_date', 'desc');

        // Apply same filters as index
        if ($request->has('type') && $request->type !== '') {
            $query->where('type', $request->type);
        }
        if ($request->has('status') && $request->status !== '') {
            $query->where('status', $request->status);
        }

        $invoices = $query->get();

        $filename = 'rechnungen_admin_' . now()->format('Y-m-d') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($invoices) {
            $file = fopen('php://output', 'w');

            // UTF-8 BOM
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));

            // Header
            fputcsv($file, [
                'Rechnungsnummer',
                'Typ',
                'Datum',
                'Fällig am',
                'Empfänger',
                'E-Mail',
                'Event',
                'Betrag (netto)',
                'MwSt.',
                'Gesamt (brutto)',
                'Status',
                'Bezahlt am',
            ], ';');

            // Data
            foreach ($invoices as $invoice) {
                fputcsv($file, [
                    $invoice->invoice_number,
                    $invoice->type === 'platform_fee' ? 'Platform-Fee' : 'Teilnehmer',
                    $invoice->invoice_date->format('d.m.Y'),
                    $invoice->due_date->format('d.m.Y'),
                    $invoice->recipient_name,
                    $invoice->recipient_email,
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

    /**
     * Delete invoice
     */
    public function destroy(Invoice $invoice)
    {
        // Delete PDF file if exists
        if ($invoice->pdf_path && file_exists(storage_path("app/{$invoice->pdf_path}"))) {
            unlink(storage_path("app/{$invoice->pdf_path}"));
        }

        $invoice->delete();

        return redirect()->route('admin.invoices.index')
            ->with('success', 'Rechnung wurde gelöscht.');
    }
}

