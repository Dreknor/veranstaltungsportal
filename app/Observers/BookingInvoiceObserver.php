<?php

namespace App\Observers;

use App\Models\Booking;
use App\Services\InvoiceNumberService;

class BookingInvoiceObserver
{
    protected InvoiceNumberService $invoiceNumberService;

    public function __construct(InvoiceNumberService $invoiceNumberService)
    {
        $this->invoiceNumberService = $invoiceNumberService;
    }

    /**
     * Handle the Booking "created" event.
     */
    public function created(Booking $booking): void
    {
        // Generate invoice number when booking is created
        if (!$booking->invoice_number) {
            $booking->invoice_number = $this->invoiceNumberService->generateBookingInvoiceNumber();
            $booking->invoice_date = now();
            $booking->saveQuietly(); // Save without triggering events again
        }
    }

    /**
     * Handle the Booking "updated" event.
     */
    public function updated(Booking $booking): void
    {
        // Generate invoice number when payment status changes to paid (if not already set)
        if ($booking->wasChanged('payment_status') &&
            $booking->payment_status === 'paid' &&
            !$booking->invoice_number) {
            $booking->invoice_number = $this->invoiceNumberService->generateBookingInvoiceNumber();
            $booking->invoice_date = now();
            $booking->saveQuietly();
        }
    }
}

