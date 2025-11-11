<?php

namespace App\Services;

use App\Models\Event;
use App\Models\User;

class EventCostCalculationService
{
    /**
     * Calculate estimated costs for publishing an event
     */
    public function calculatePublishingCosts(Event $event, User $user): array
    {
        $costs = [
            'platform_fees' => $this->calculateEstimatedPlatformFees($event, $user),
            'featured_fees' => $this->calculateFeaturedFees($event),
            'total' => 0,
            'breakdown' => [],
        ];

        $costs['total'] = $costs['platform_fees']['total'] + $costs['featured_fees']['total'];

        $costs['breakdown'] = $this->buildCostBreakdown($costs);

        return $costs;
    }

    /**
     * Calculate estimated platform fees based on expected ticket sales
     */
    private function calculateEstimatedPlatformFees(Event $event, User $user): array
    {
        // Get organizer's fee settings
        $feePercentage = $this->getOrganizerFeePercentage($user);
        $feeType = $this->getOrganizerFeeType($user);
        $minimumFee = $this->getOrganizerMinimumFee($user);

        // Estimate based on ticket types or max attendees
        $estimatedRevenue = $this->estimateEventRevenue($event);
        $estimatedBookings = $this->estimateBookingCount($event);

        if ($feeType === 'percentage') {
            $platformFee = $estimatedRevenue * ($feePercentage / 100);
        } else {
            $fixedAmount = $this->getOrganizerFixedFee($user);
            $platformFee = $fixedAmount * $estimatedBookings;
        }

        // Apply minimum fee per booking
        $minimumTotalFee = $minimumFee * $estimatedBookings;
        if ($platformFee < $minimumTotalFee) {
            $platformFee = $minimumTotalFee;
        }

        return [
            'type' => $feeType,
            'percentage' => $feePercentage,
            'minimum_fee' => $minimumFee,
            'estimated_revenue' => $estimatedRevenue,
            'estimated_bookings' => $estimatedBookings,
            'fee_per_booking' => $feeType === 'percentage'
                ? max(($estimatedRevenue / max($estimatedBookings, 1)) * ($feePercentage / 100), $minimumFee)
                : max($this->getOrganizerFixedFee($user), $minimumFee),
            'total' => round($platformFee, 2),
            'description' => $this->buildFeeDescription($feeType, $feePercentage, $estimatedRevenue, $estimatedBookings, $minimumFee, $this->getOrganizerFixedFee($user)),
        ];
    }

    /**
     * Build fee description
     */
    private function buildFeeDescription($feeType, $feePercentage, $estimatedRevenue, $estimatedBookings, $minimumFee, $fixedAmount): string
    {
        if ($feeType === 'percentage') {
            $calculatedFee = $estimatedRevenue * ($feePercentage / 100);
            $minimumTotal = $minimumFee * $estimatedBookings;

            if ($calculatedFee < $minimumTotal) {
                return "Mindestgebühr: " . number_format($minimumFee, 2, ',', '.') . " € pro Buchung × {$estimatedBookings} = " . number_format($minimumTotal, 2, ',', '.') . " €";
            }

            return "Ca. {$feePercentage}% von geschätzten " . number_format($estimatedRevenue, 2, ',', '.') . " € Umsatz (min. " . number_format($minimumFee, 2, ',', '.') . " € pro Buchung)";
        } else {
            $actualFee = max($fixedAmount, $minimumFee);
            return "Ca. " . number_format($actualFee, 2, ',', '.') . " € pro Buchung × {$estimatedBookings} Buchungen";
        }
    }

    /**
     * Calculate featured event fees if event is set as featured
     */
    private function calculateFeaturedFees(Event $event): array
    {
        if (!$event->is_featured) {
            return [
                'active' => false,
                'total' => 0,
                'periods' => [],
            ];
        }

        // Get active or pending featured fees
        $featuredFees = $event->featuredFees()
            ->whereIn('payment_status', ['pending', 'paid'])
            ->where('featured_end_date', '>=', now())
            ->get();

        $total = 0;
        $periods = [];

        if ($featuredFees->isEmpty()) {
            // No fees exist yet - show estimated costs for weekly featured (default)
            $weeklyRate = config('monetization.featured_event_rates.weekly', 25.00);

            $periods[] = [
                'duration_type' => 'weekly',
                'duration_days' => 7,
                'start_date' => now(),
                'end_date' => now()->addWeek(),
                'amount' => $weeklyRate,
                'status' => 'estimated',
            ];

            $total = $weeklyRate;
        } else {
            foreach ($featuredFees as $fee) {
                $periods[] = [
                    'duration_type' => $fee->duration_type,
                    'duration_days' => $fee->duration_days,
                    'start_date' => $fee->featured_start_date,
                    'end_date' => $fee->featured_end_date,
                    'amount' => $fee->fee_amount,
                    'status' => $fee->payment_status,
                ];

                if ($fee->payment_status === 'pending') {
                    $total += $fee->fee_amount;
                }
            }
        }

        return [
            'active' => true,
            'total' => round($total, 2),
            'periods' => $periods,
        ];
    }

    /**
     * Estimate event revenue based on ticket types
     */
    private function estimateEventRevenue(Event $event): float
    {
        $ticketTypes = $event->ticketTypes;

        if ($ticketTypes->isEmpty()) {
            // Fallback to price_from * max_attendees
            return ($event->price_from ?? 0) * ($event->max_attendees ?? 10);
        }

        $totalRevenue = 0;

        foreach ($ticketTypes as $ticketType) {
            $quantity = $ticketType->quantity ?? ($event->max_attendees ?? 10);
            $totalRevenue += $ticketType->price * $quantity;
        }

        return $totalRevenue;
    }

    /**
     * Estimate number of bookings
     */
    private function estimateBookingCount(Event $event): int
    {
        $ticketTypes = $event->ticketTypes;

        if ($ticketTypes->isEmpty()) {
            return $event->max_attendees ?? 10;
        }

        return $ticketTypes->sum('quantity') ?: ($event->max_attendees ?? 10);
    }

    /**
     * Build cost breakdown for display
     */
    private function buildCostBreakdown(array $costs): array
    {
        $breakdown = [];

        if ($costs['platform_fees']['total'] > 0) {
            $breakdown[] = [
                'label' => 'Plattformgebühren (geschätzt)',
                'amount' => $costs['platform_fees']['total'],
                'description' => $costs['platform_fees']['description'],
                'type' => 'platform_fee',
            ];
        }

        if ($costs['featured_fees']['active'] && $costs['featured_fees']['total'] > 0) {
            foreach ($costs['featured_fees']['periods'] as $period) {
                if ($period['status'] === 'pending' || $period['status'] === 'estimated') {
                    $durationLabel = $this->formatDurationLabel($period);
                    $breakdown[] = [
                        'label' => 'Featured Event Gebühr' . ($period['status'] === 'estimated' ? ' (geschätzt)' : ''),
                        'amount' => $period['amount'],
                        'description' => $period['status'] === 'estimated'
                            ? "Standardpreis für 7 Tage Featured-Zeitraum (bitte separat buchen)"
                            : "Zeitraum: {$period['start_date']->format('d.m.Y')} - {$period['end_date']->format('d.m.Y')} ({$durationLabel})",
                        'type' => 'featured_fee',
                        'status' => $period['status'],
                    ];
                }
            }
        }

        return $breakdown;
    }

    /**
     * Format duration label
     */
    private function formatDurationLabel(array $period): string
    {
        return match($period['duration_type']) {
            'daily' => '1 Tag',
            'weekly' => '7 Tage',
            'monthly' => '30 Tage',
            'custom' => $period['duration_days'] . ' Tage',
            default => 'Custom',
        };
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

    /**
     * Get platform fee information for display (without event-specific data)
     */
    public function getPlatformFeeInfo(User $user): array
    {
        $feeType = $this->getOrganizerFeeType($user);
        $feePercentage = $this->getOrganizerFeePercentage($user);
        $fixedFee = $this->getOrganizerFixedFee($user);
        $minimumFee = $this->getOrganizerMinimumFee($user);

        $description = $feeType === 'percentage'
            ? "Plattformgebühr: {$feePercentage}% vom Buchungsumsatz (mind. " . number_format($minimumFee, 2, ',', '.') . " € pro Buchung)"
            : "Plattformgebühr: " . number_format(max($fixedFee, $minimumFee), 2, ',', '.') . " € pro Buchung";

        return [
            'type' => $feeType,
            'percentage' => $feePercentage,
            'fixed_amount' => $fixedFee,
            'minimum_fee' => $minimumFee,
            'description' => $description,
        ];
    }
}
