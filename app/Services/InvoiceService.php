<?php

namespace App\Services;

use App\Models\Event;
use App\Models\Booking;
use App\Models\Invoice;
use App\Models\PlatformFee;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class InvoiceService
{
    /**
     * Generate platform fee invoice after event ends
     */
    public function generatePlatformFeeInvoice(Event $event)
    {
        // Check if event has ended
        if (!$event->end_date || !$event->end_date->isPast()) {
            return null;
        }

        // Check if invoice already exists
        if (Invoice::where('event_id', $event->id)->where('type', 'platform_fee')->exists()) {
            return Invoice::where('event_id', $event->id)->where('type', 'platform_fee')->first();
        }

        // Calculate total platform fees
        $totalFees = PlatformFee::where('event_id', $event->id)->sum('fee_amount');

        if ($totalFees <= 0) {
            return null;
        }

        // Get platform fee percentage (check for custom fee first)
        $feePercentage = $this->getOrganizerFeePercentage($event->user);

        // Create invoice
        $invoice = Invoice::create([
            'invoice_number' => $this->generateInvoiceNumber('PF'),
            'event_id' => $event->id,
            'user_id' => $event->user_id,
            'type' => 'platform_fee',
            'recipient_name' => $event->user->organization_name ?? $event->user->name,
            'recipient_email' => $event->user->email,
            'recipient_address' => $this->formatOrganizerAddress($event->user),
            'amount' => $totalFees,
            'tax_rate' => 19.0, // German VAT
            'tax_amount' => $totalFees * 0.19,
            'total_amount' => $totalFees * 1.19,
            'currency' => 'EUR',
            'invoice_date' => now(),
            'due_date' => now()->addDays((int) config('monetization.payment_deadline_days', 14)),
            'status' => 'sent',
            'billing_data' => [
                'platform' => $this->getPlatformBillingData(),
                'organizer' => $this->getOrganizerBillingData($event->user),
                'items' => $this->getPlatformFeeItems($event, $feePercentage),
            ],
        ]);

        // Generate PDF
        $this->generateInvoicePDF($invoice);

        // Send invoice email
        if (config('monetization.auto_invoice', true)) {
            $this->sendInvoiceEmail($invoice);
        }

        return $invoice;
    }

    /**
     * Generate participant payment invoice
     */
    public function generateParticipantInvoice(Booking $booking)
    {
        // Check if invoice already exists
        if (Invoice::where('booking_id', $booking->id)->where('type', 'participant')->exists()) {
            return Invoice::where('booking_id', $booking->id)->where('type', 'participant')->first();
        }

        $invoice = Invoice::create([
            'invoice_number' => $this->generateInvoiceNumber('TN'),
            'event_id' => $booking->event_id,
            'booking_id' => $booking->id,
            'user_id' => $booking->event->user_id, // Organizer
            'type' => 'participant',
            'recipient_name' => $booking->customer_name,
            'recipient_email' => $booking->customer_email,
            'recipient_address' => $this->formatBookingAddress($booking),
            'amount' => $booking->subtotal,
            'tax_rate' => 19.0,
            'tax_amount' => $booking->subtotal * 0.19,
            'total_amount' => $booking->total,
            'currency' => 'EUR',
            'invoice_date' => now(),
            'due_date' => $booking->event->start_date ? $booking->event->start_date->copy()->subDays(7) : now()->addDays(7),
            'status' => 'sent',
            'billing_data' => [
                'organizer' => $this->getOrganizerBillingData($booking->event->user),
                'participant' => [
                    'name' => $booking->customer_name,
                    'email' => $booking->customer_email,
                    'address' => $this->formatBookingAddress($booking),
                ],
                'items' => $this->getBookingItems($booking),
                'bank_account' => $booking->event->user->bank_account ?? [],
            ],
        ]);

        $this->generateInvoicePDF($invoice);
        $this->sendInvoiceEmail($invoice);

        return $invoice;
    }

    /**
     * Generate unique invoice number
     */
    public function generateInvoiceNumber($prefix = 'TN')
    {
        $year = date('Y');
        $lastInvoice = Invoice::where('invoice_number', 'like', "{$prefix}-{$year}-%")->latest()->first();

        if ($lastInvoice) {
            $lastNumber = (int) substr($lastInvoice->invoice_number, -5);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return sprintf('%s-%s-%05d', $prefix, $year, $newNumber);
    }

    /**
     * Get platform billing data
     */
    private function getPlatformBillingData()
    {
        return [
            'company_name' => config('monetization.platform_company_name', ''),
            'address' => config('monetization.platform_company_address', ''),
            'postal_code' => config('monetization.platform_company_postal_code', ''),
            'city' => config('monetization.platform_company_city', ''),
            'country' => config('monetization.platform_company_country', ''),
            'tax_id' => config('monetization.platform_tax_id', ''),
            'vat_id' => config('monetization.platform_vat_id', ''),
            'email' => config('monetization.platform_company_email', ''),
            'phone' => config('monetization.platform_company_phone', ''),
            'bank_name' => config('monetization.platform_bank_name', ''),
            'iban' => config('monetization.platform_bank_iban', ''),
            'bic' => config('monetization.platform_bank_bic', ''),
        ];
    }

    /**
     * Get organizer billing data
     */
    private function getOrganizerBillingData($user)
    {
        $billingData = $user->organizer_billing_data ?? [];

        return [
            'company_name' => $billingData['company_name'] ?? $user->organization_name ?? $user->name,
            'address' => $billingData['company_address'] ?? '',
            'postal_code' => $billingData['company_postal_code'] ?? '',
            'city' => $billingData['company_city'] ?? '',
            'country' => $billingData['company_country'] ?? 'Deutschland',
            'tax_id' => $billingData['tax_id'] ?? $user->tax_id ?? '',
            'vat_id' => $billingData['vat_id'] ?? '',
            'email' => $billingData['company_email'] ?? $user->email,
            'phone' => $billingData['company_phone'] ?? $user->phone ?? '',
        ];
    }

    /**
     * Format organizer address
     */
    private function formatOrganizerAddress($user)
    {
        $billingData = $user->organizer_billing_data ?? [];

        $address = $billingData['company_address'] ?? '';
        $postal = $billingData['company_postal_code'] ?? '';
        $city = $billingData['company_city'] ?? '';
        $country = $billingData['company_country'] ?? '';

        return trim("{$address}\n{$postal} {$city}\n{$country}");
    }

    /**
     * Format booking address
     */
    private function formatBookingAddress($booking)
    {
        return trim("{$booking->billing_address}\n{$booking->billing_postal_code} {$booking->billing_city}\n{$booking->billing_country}");
    }

    /**
     * Get platform fee items for invoice
     */
    private function getPlatformFeeItems($event, $feePercentage)
    {
        $fees = PlatformFee::where('event_id', $event->id)->get();

        // Verwende die tatsächliche Fee-Percentage aus den Fees (durchschnitt)
        $actualFeePercentage = $fees->isNotEmpty() ? $fees->avg('fee_percentage') : $feePercentage;

        return [
            [
                'description' => "Plattformgebühr für Event: {$event->title}",
                'quantity' => $fees->count(),
                'unit_price' => $fees->avg('fee_amount'),
                'total' => $fees->sum('fee_amount'),
                'details' => "Gebühr: " . number_format($actualFeePercentage, 2) . "% pro Buchung",
            ]
        ];
    }

    /**
     * Get booking items for invoice
     */
    private function getBookingItems($booking)
    {
        return $booking->items->map(function ($item) {
            return [
                'description' => $item->ticketType->name ?? 'Ticket',
                'quantity' => $item->quantity,
                'unit_price' => $item->price,
                'total' => $item->quantity * $item->price,
            ];
        })->toArray();
    }

    /**
     * Generate invoice PDF
     */
    private function generateInvoicePDF($invoice)
    {
        $pdf = PDF::loadView('invoices.pdf', compact('invoice'));

        $filename = "invoice-{$invoice->invoice_number}.pdf";
        $path = storage_path("app/invoices/{$filename}");

        if (!file_exists(storage_path('app/invoices'))) {
            mkdir(storage_path('app/invoices'), 0755, true);
        }

        $pdf->save($path);

        $invoice->update([
            'pdf_path' => "invoices/{$filename}"
        ]);

        return $path;
    }

    /**
     * Send invoice email
     */
    private function sendInvoiceEmail($invoice)
    {
        $ccEmail = config('monetization.invoice_cc_email');

        Mail::send('emails.invoice', compact('invoice'), function ($message) use ($invoice, $ccEmail) {
            $message->to($invoice->recipient_email)
                    ->subject("Rechnung {$invoice->invoice_number}");

            if ($ccEmail) {
                $message->cc($ccEmail);
            }

            if ($invoice->pdf_path && file_exists(storage_path("app/{$invoice->pdf_path}"))) {
                $message->attach(storage_path("app/{$invoice->pdf_path}"));
            }
        });
    }

    /**
     * Get organizer's fee percentage (custom or global)
     */
    private function getOrganizerFeePercentage($user)
    {
        // Check if organizer has custom fee settings
        if (!empty($user->custom_platform_fee) && ($user->custom_platform_fee['enabled'] ?? false)) {
            $customFee = $user->custom_platform_fee;

            if ($customFee['fee_type'] === 'percentage') {
                return $customFee['fee_percentage'] ?? config('monetization.platform_fee_percentage', 5.0);
            }
        }

        // Return global setting
        return config('monetization.platform_fee_percentage', 5.0);
    }

    /**
     * Get organizer's fee amount for a booking
     */
    private function getOrganizerFeeAmount($user, $bookingTotal)
    {
        // Check if organizer has custom fee settings
        if (!empty($user->custom_platform_fee) && ($user->custom_platform_fee['enabled'] ?? false)) {
            $customFee = $user->custom_platform_fee;

            if ($customFee['fee_type'] === 'fixed') {
                return $customFee['fee_fixed_amount'] ?? 0;
            } elseif ($customFee['fee_type'] === 'percentage') {
                $percentage = $customFee['fee_percentage'] ?? 0;
                return $bookingTotal * ($percentage / 100);
            }
        }

        // Return global setting calculation
        $feeType = config('monetization.platform_fee_type', 'percentage');

        if ($feeType === 'fixed') {
            return config('monetization.platform_fee_fixed_amount', 0);
        } else {
            $percentage = config('monetization.platform_fee_percentage', 5.0);
            return $bookingTotal * ($percentage / 100);
        }
    }

    /**
     * Generate invoice PDF output for booking
     * Returns PDF content as string for email attachment
     */
    public function getInvoiceOutput(Booking $booking)
    {
        $invoice = [
            'booking' => $booking,
            'event' => $booking->event,
            'items' => $this->getBookingItems($booking),
            'organizer' => $this->getOrganizerBillingData($booking->event->user),
            'customer' => [
                'name' => $booking->customer_name,
                'email' => $booking->customer_email,
                'address' => $this->formatBookingAddress($booking),
            ],
        ];

        $pdf = PDF::loadView('invoices.booking-pdf', $invoice);
        return $pdf->output();
    }
}

