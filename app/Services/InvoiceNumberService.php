<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\DB;

class InvoiceNumberService
{
    /**
     * Generate invoice number for booking
     */
    public function generateBookingInvoiceNumber(): string
    {
        return $this->generateInvoiceNumber('booking');
    }

    /**
     * Generate invoice number for platform fee
     */
    public function generatePlatformFeeInvoiceNumber(): string
    {
        return $this->generateInvoiceNumber('platform_fee');
    }

    /**
     * Generate invoice number based on type
     */
    protected function generateInvoiceNumber(string $type): string
    {
        $formatKey = "invoice_number_format_{$type}";
        $counterKey = "invoice_number_counter_{$type}";

        // Get settings
        $format = Setting::getValue($formatKey, "RE-{YEAR}-{NUMBER}");
        $padding = (int) Setting::getValue('invoice_number_padding', 5);
        $resetYearly = Setting::getValue('invoice_reset_yearly', true);

        // Get current counter with lock to prevent race conditions
        $counter = DB::transaction(function () use ($counterKey, $resetYearly, $type) {
            $currentYear = now()->format('Y');
            $lastYear = Setting::getValue("{$counterKey}_year", $currentYear);

            // Reset counter if yearly reset is enabled and year changed
            if ($resetYearly && $lastYear !== $currentYear) {
                Setting::setValue($counterKey, 1);
                Setting::setValue("{$counterKey}_year", $currentYear);
                return 1;
            }

            // Increment counter
            $counter = (int) Setting::getValue($counterKey, 1);
            Setting::setValue($counterKey, $counter + 1);

            return $counter;
        });

        // Replace placeholders
        $invoiceNumber = $this->replacePlaceholders($format, $counter, $padding);

        return $invoiceNumber;
    }

    /**
     * Replace placeholders in format string
     */
    protected function replacePlaceholders(string $format, int $counter, int $padding): string
    {
        $now = now();

        $placeholders = [
            '{YEAR}' => $now->format('Y'),
            '{YEAR_SHORT}' => $now->format('y'),
            '{MONTH}' => $now->format('m'),
            '{DAY}' => $now->format('d'),
            '{NUMBER}' => str_pad($counter, $padding, '0', STR_PAD_LEFT),
            '{COUNTER}' => $counter,
        ];

        return str_replace(array_keys($placeholders), array_values($placeholders), $format);
    }

    /**
     * Get available placeholders
     */
    public function getAvailablePlaceholders(): array
    {
        return [
            '{YEAR}' => 'Aktuelles Jahr (vierstellig, z.B. 2025)',
            '{YEAR_SHORT}' => 'Aktuelles Jahr (zweistellig, z.B. 25)',
            '{MONTH}' => 'Aktueller Monat (zweistellig, z.B. 01-12)',
            '{DAY}' => 'Aktueller Tag (zweistellig, z.B. 01-31)',
            '{NUMBER}' => 'Fortlaufende Nummer mit Nullen aufgefüllt',
            '{COUNTER}' => 'Fortlaufende Nummer ohne Auffüllung',
        ];
    }

    /**
     * Validate format string
     */
    public function validateFormat(string $format): bool
    {
        // Must contain {NUMBER} or {COUNTER}
        if (!str_contains($format, '{NUMBER}') && !str_contains($format, '{COUNTER}')) {
            return false;
        }

        // Check for invalid placeholders
        $validPlaceholders = array_keys($this->getAvailablePlaceholders());
        preg_match_all('/\{([A-Z_]+)\}/', $format, $matches);

        foreach ($matches[0] as $placeholder) {
            if (!in_array($placeholder, $validPlaceholders)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Preview invoice number
     */
    public function previewFormat(string $format, int $padding = 5): string
    {
        return $this->replacePlaceholders($format, 1, $padding);
    }
}

