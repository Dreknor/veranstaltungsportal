<?php

namespace App\Services;

use App\Models\Setting;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class InvoiceNumberService
{
    /**
     * Generate invoice number for booking (issued by organizer to participant)
     * Uses organizer-specific settings
     *
     * @param User $organizer The organizer user (will use their current organization)
     * @return string Generated invoice number
     */
    public function generateBookingInvoiceNumber(User $organizer): string
    {
        // Get the organizer's current organization
        $organization = $organizer->currentOrganization();

        if (!$organization) {
            throw new \Exception('Organizer must have an active organization to generate invoice numbers.');
        }

        // Get organization-specific invoice settings
        $settings = $organization->invoice_settings ?? [];

        $format = $settings['invoice_number_format_booking'] ?? "RE-{YEAR}-{NUMBER}";
        $padding = (int) ($settings['invoice_number_padding'] ?? 5);
        $resetYearly = $settings['invoice_reset_yearly'] ?? true;

        // Get current counter with lock to prevent race conditions
        $counter = DB::transaction(function () use ($organization, $resetYearly) {
            // Reload organization to get fresh data with lock
            $org = \App\Models\Organization::lockForUpdate()->find($organization->id);

            $currentYear = now()->format('Y');
            $lastYear = $org->invoice_counter_booking_year ?? $currentYear;
            $counter = $org->invoice_counter_booking ?? 1;

            // Reset counter if yearly reset is enabled and year changed
            if ($resetYearly && $lastYear !== $currentYear) {
                $org->invoice_counter_booking = 2; // Next number after reset
                $org->invoice_counter_booking_year = $currentYear;
                $org->save();
                return 1;
            }

            // Increment counter
            $currentCounter = $counter;
            $org->invoice_counter_booking = $counter + 1;
            $org->save();

            return $currentCounter;
        });

        // Replace placeholders
        return $this->replacePlaceholders($format, $counter, $padding);
    }

    /**
     * Generate invoice number for platform fee (issued by platform to organizer)
     * Uses global platform settings
     */
    public function generatePlatformFeeInvoiceNumber(): string
    {
        // Get global platform settings
        $format = Setting::getValue('invoice_number_format_platform_fee', 'PF-{YEAR}-{NUMBER}');
        $padding = (int) Setting::getValue('invoice_number_padding', 5);
        $resetYearly = Setting::getValue('invoice_reset_yearly', true);

        // Get current counter with lock to prevent race conditions
        $counter = DB::transaction(function () use ($resetYearly) {
            $currentYear = now()->format('Y');
            $lastYear = Setting::getValue('invoice_counter_platform_fee_year', $currentYear);

            // Reset counter if yearly reset is enabled and year changed
            if ($resetYearly && $lastYear !== $currentYear) {
                Setting::setValue('invoice_counter_platform_fee', 1);
                Setting::setValue('invoice_counter_platform_fee_year', $currentYear);
                return 1;
            }

            // Increment counter
            $counter = (int) Setting::getValue('invoice_counter_platform_fee', 1);
            Setting::setValue('invoice_counter_platform_fee', $counter + 1);

            return $counter;
        });

        // Replace placeholders
        return $this->replacePlaceholders($format, $counter, $padding);
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

    /**
     * Get next booking invoice number WITHOUT incrementing the counter
     * Used for sample/preview invoices
     */
    public function previewNextBookingInvoiceNumber(User $organizer): string
    {
        $organization = $organizer->currentOrganization();

        if (!$organization) {
            throw new \Exception('Organizer must have an active organization to generate invoice numbers.');
        }

        // Get organization-specific invoice settings
        $settings = $organization->invoice_settings ?? [];

        $format = $settings['invoice_number_format_booking'] ?? "RE-{YEAR}-{NUMBER}";
        $padding = (int) ($settings['invoice_number_padding'] ?? 5);
        $resetYearly = $settings['invoice_reset_yearly'] ?? true;

        // Get the next counter WITHOUT incrementing it
        $currentYear = now()->format('Y');
        $lastYear = $organization->invoice_counter_booking_year ?? $currentYear;
        $currentCounter = $organization->invoice_counter_booking ?? 1;

        // If yearly reset is enabled and year has changed, use 1
        if ($resetYearly && $lastYear !== $currentYear) {
            $nextCounter = 1;
        } else {
            $nextCounter = $currentCounter;
        }

        // Return the preview number
        return $this->replacePlaceholders($format, $nextCounter, $padding);
    }
}

