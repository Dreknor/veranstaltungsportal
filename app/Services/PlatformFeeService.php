<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\PlatformFee;
use App\Models\User;

class PlatformFeeService
{
    /**
     * Calculate and create platform fee for a booking
     */
    public function calculateAndCreateFee(Booking $booking): ?PlatformFee
    {
        $event = $booking->event;
        $organizer = $event->user;

        // Get organizer's fee settings
        $feeType = $this->getOrganizerFeeType($organizer);
        $feePercentage = $this->getOrganizerFeePercentage($organizer);
        $fixedFee = $this->getOrganizerFixedFee($organizer);
        $minimumFee = $this->getOrganizerMinimumFee($organizer);

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
     * Get organizer's fee type
     */
    private function getOrganizerFeeType(User $user): string
    {
        if (!empty($user->custom_platform_fee) && ($user->custom_platform_fee['enabled'] ?? false)) {
            return $user->custom_platform_fee['fee_type'] ?? 'percentage';
        }

        return config('monetization.platform_fee_type', 'percentage');
    }

    /**
     * Get organizer's fee percentage
     */
    private function getOrganizerFeePercentage(User $user): float
    {
        if (!empty($user->custom_platform_fee) && ($user->custom_platform_fee['enabled'] ?? false)) {
            $customFee = $user->custom_platform_fee;
            if ($customFee['fee_type'] === 'percentage') {
                return $customFee['fee_percentage'] ?? config('monetization.platform_fee_percentage', 5.0);
            }
        }

        return config('monetization.platform_fee_percentage', 5.0);
    }

    /**
     * Get organizer's fixed fee amount
     */
    private function getOrganizerFixedFee(User $user): float
    {
        if (!empty($user->custom_platform_fee) && ($user->custom_platform_fee['enabled'] ?? false)) {
            $customFee = $user->custom_platform_fee;
            if ($customFee['fee_type'] === 'fixed') {
                return $customFee['fee_fixed_amount'] ?? 0;
            }
        }

        return config('monetization.platform_fee_fixed_amount', 0);
    }

    /**
     * Get organizer's minimum fee
     */
    private function getOrganizerMinimumFee(User $user): float
    {
        if (!empty($user->custom_platform_fee) && ($user->custom_platform_fee['enabled'] ?? false)) {
            $customFee = $user->custom_platform_fee;
            if (isset($customFee['minimum_fee'])) {
                return (float) $customFee['minimum_fee'];
            }
        }

        return config('monetization.platform_fee_minimum', 1.00);
    }
}

