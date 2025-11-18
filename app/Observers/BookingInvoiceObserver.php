<?php

namespace App\Observers;

use App\Models\Booking;
use App\Services\InvoiceNumberService;
use Illuminate\Support\Facades\Log;

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
        if (!$booking->invoice_number && $booking->event) {
            try {
                // Get the organizer user from the event
                $organizer = $booking->event->getUser();

                if ($organizer) {
                    $booking->invoice_number = $this->invoiceNumberService->generateBookingInvoiceNumber($organizer);
                    $booking->invoice_date = now();
                    $booking->saveQuietly(); // Save without triggering events again
                }
            } catch (\Exception $e) {
                // Log error but don't fail the booking creation
                Log::error('Failed to generate invoice number for booking: ' . $e->getMessage(), [
                    'booking_id' => $booking->id,
                    'event_id' => $booking->event_id,
                ]);
            }
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
            !$booking->invoice_number &&
            $booking->event) {
            try {
                // Get the organizer user from the event
                $organizer = $booking->event->getUser();

                if ($organizer) {
                    $booking->invoice_number = $this->invoiceNumberService->generateBookingInvoiceNumber($organizer);
                    $booking->invoice_date = now();
                    $booking->saveQuietly();
                }
            } catch (\Exception $e) {
                // Log error but don't fail the booking update
                Log::error('Failed to generate invoice number for booking: ' . $e->getMessage(), [
                    'booking_id' => $booking->id,
                    'event_id' => $booking->event_id,
                ]);
            }
        }
    }
}

