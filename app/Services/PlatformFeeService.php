<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\PlatformFee;

class PlatformFeeService
{
    /**
     * Calculate and create platform fee for a booking
     */
    public function calculateAndCreateFee(Booking $booking): ?PlatformFee
    {
        $event = $booking->event;
        $organization = $event->organization;

        // Get organization's fee settings
        $feeType = $this->getOrganizationFeeType($organization);
        $feePercentage = $this->getOrganizationFeePercentage($organization);
        $fixedFee = $this->getOrganizationFixedFee($organization);
        $minimumFee = $this->getOrganizationMinimumFee($organization);

        $bookingAmount = $booking->total;

        // Calculate fee based on type
        if ($feeType === 'percentage') {
            $calculatedFee = $bookingAmount * ($feePercentage / 100);
        } else {
            $calculatedFee = $fixedFee;
        }

        // Apply minimum fee
        $feeAmount = max($calculatedFee, $minimumFee);

        // Create platform fee record
        return PlatformFee::create([
            'event_id' => $event->id,
            'booking_id' => $booking->id,
            'fee_percentage' => $feePercentage,
            'booking_amount' => $bookingAmount,
            'fee_amount' => $feeAmount,
            'minimum_fee' => $minimumFee,
            'status' => 'pending',
        ]);
    }

    /**
     * Get organization's fee type
     */
    private function getOrganizationFeeType($organization): string
    {
        if ($organization && !empty($organization->custom_platform_fee) && ($organization->custom_platform_fee['enabled'] ?? false)) {
            return $organization->custom_platform_fee['fee_type'] ?? 'percentage';
        }

        return config('monetization.platform_fee_type', 'percentage');
    }

    /**
     * Get organization's fee percentage
     */
    private function getOrganizationFeePercentage($organization): float
    {
        if ($organization && !empty($organization->custom_platform_fee) && ($organization->custom_platform_fee['enabled'] ?? false)) {
            $customFee = $organization->custom_platform_fee;
            if ($customFee['fee_type'] === 'percentage') {
                return $customFee['fee_percentage'] ?? config('monetization.platform_fee_percentage', 5.0);
            }
        }

        return config('monetization.platform_fee_percentage', 5.0);
    }

    /**
     * Get organization's fixed fee amount
     */
    private function getOrganizationFixedFee($organization): float
    {
        if ($organization && !empty($organization->custom_platform_fee) && ($organization->custom_platform_fee['enabled'] ?? false)) {
            $customFee = $organization->custom_platform_fee;
            if ($customFee['fee_type'] === 'fixed') {
                return $customFee['fee_fixed_amount'] ?? 0;
            }
        }

        return config('monetization.platform_fee_fixed_amount', 0);
    }

    /**
     * Get organization's minimum fee
     */
    private function getOrganizationMinimumFee($organization): float
    {
        if ($organization && !empty($organization->custom_platform_fee) && ($organization->custom_platform_fee['enabled'] ?? false)) {
            $customFee = $organization->custom_platform_fee;
            if (isset($customFee['minimum_fee'])) {
                return (float) $customFee['minimum_fee'];
            }
        }

        return config('monetization.platform_fee_minimum', 1.00);
    }
}

