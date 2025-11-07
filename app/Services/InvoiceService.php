<?php

namespace App\Services;

use App\Models\Booking;
use Barryvdh\DomPDF\Facade\Pdf;

class InvoiceService
{
    /**
     * Generate invoice PDF for a booking
     */
    public function generateInvoice(Booking $booking): \Barryvdh\DomPDF\PDF
    {
        $data = [
            'booking' => $booking,
            'event' => $booking->event,
            'items' => $booking->items()->with('ticketType')->get(),
            'invoice_number' => $this->generateInvoiceNumber($booking),
            'invoice_date' => now(),
        ];

        return Pdf::loadView('invoices.booking', $data);
    }

    /**
     * Generate unique invoice number
     */
    public function generateInvoiceNumber(Booking $booking): string
    {
        $year = $booking->created_at->format('Y');
        $month = $booking->created_at->format('m');

        // Format: INV-2025-11-00001
        return sprintf(
            'INV-%s-%s-%s',
            $year,
            $month,
            str_pad($booking->id, 5, '0', STR_PAD_LEFT)
        );
    }

    /**
     * Download invoice as PDF
     */
    public function downloadInvoice(Booking $booking): \Symfony\Component\HttpFoundation\Response
    {
        $pdf = $this->generateInvoice($booking);
        $filename = sprintf('Rechnung_%s.pdf', $this->generateInvoiceNumber($booking));

        return $pdf->download($filename);
    }

    /**
     * Stream invoice (view in browser)
     */
    public function streamInvoice(Booking $booking): \Symfony\Component\HttpFoundation\Response
    {
        $pdf = $this->generateInvoice($booking);

        return $pdf->stream();
    }

    /**
     * Get invoice as string for email attachment
     */
    public function getInvoiceOutput(Booking $booking): string
    {
        $pdf = $this->generateInvoice($booking);

        return $pdf->output();
    }
}

