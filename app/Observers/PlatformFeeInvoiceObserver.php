<?php

namespace App\Observers;

use App\Models\PlatformFee;
use App\Services\InvoiceNumberService;

class PlatformFeeInvoiceObserver
{
    protected InvoiceNumberService $invoiceNumberService;

    public function __construct(InvoiceNumberService $invoiceNumberService)
    {
        $this->invoiceNumberService = $invoiceNumberService;
    }

    /**
     * Handle the PlatformFee "created" event.
     */
    public function created(PlatformFee $platformFee): void
    {
        // Generate invoice number when platform fee is created
        if (!$platformFee->invoice_number) {
            $platformFee->invoice_number = $this->invoiceNumberService->generatePlatformFeeInvoiceNumber();
            $platformFee->invoice_date = now();
            $platformFee->saveQuietly(); // Save without triggering events again
        }
    }
}

