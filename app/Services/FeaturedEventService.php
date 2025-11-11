<?php

namespace App\Services;

use App\Models\Event;
use App\Models\FeaturedEventFee;
use App\Models\User;
use Carbon\Carbon;

class FeaturedEventService
{
    /**
     * Calculate the fee for featuring an event
     */
    public function calculateFee(string $durationType, int $customDays = null): float
    {
        return FeaturedEventFee::calculateFee($durationType, $customDays);
    }

    /**
     * Calculate end date based on duration type and start date
     */
    public function calculateEndDate(Carbon $startDate, string $durationType, int $customDays = null): Carbon
    {
        return match($durationType) {
            'daily' => $startDate->copy()->addDay(),
            'weekly' => $startDate->copy()->addWeek(),
            'monthly' => $startDate->copy()->addMonth(),
            'custom' => $customDays ? $startDate->copy()->addDays($customDays) : $startDate,
            default => $startDate,
        };
    }

    /**
     * Create a featured event fee request
     */
    public function createFeaturedRequest(
        Event $event,
        User $user,
        string $durationType,
        Carbon $startDate,
        int $customDays = null
    ): FeaturedEventFee {
        $endDate = $this->calculateEndDate($startDate, $durationType, $customDays);
        $feeAmount = $this->calculateFee($durationType, $customDays);

        return FeaturedEventFee::create([
            'event_id' => $event->id,
            'user_id' => $user->id,
            'duration_type' => $durationType,
            'duration_days' => $customDays,
            'featured_start_date' => $startDate,
            'featured_end_date' => $endDate,
            'fee_amount' => $feeAmount,
            'payment_status' => 'pending',
        ]);
    }

    /**
     * Mark featured fee as paid and enable featured status on event
     */
    public function markAsPaid(
        FeaturedEventFee $featuredFee,
        string $paymentMethod = null,
        string $paymentReference = null
    ): void {
        $featuredFee->markAsPaid($paymentMethod, $paymentReference);

        // Enable featured status on the event
        if ($featuredFee->isActive()) {
            $featuredFee->event->update(['is_featured' => true]);
        }
    }

    /**
     * Disable expired featured events
     */
    public function disableExpiredFeaturedEvents(): int
    {
        $count = 0;

        if (!config('monetization.featured_event_auto_disable_on_expiry')) {
            return $count;
        }

        $expiredFees = FeaturedEventFee::where('payment_status', 'paid')
            ->where('featured_end_date', '<', now())
            ->with('event')
            ->get();

        foreach ($expiredFees as $fee) {
            if ($fee->event && $fee->event->is_featured) {
                $fee->event->update(['is_featured' => false]);
                $count++;
            }
        }

        return $count;
    }

    /**
     * Get active featured events
     */
    public function getActiveFeaturedEvents()
    {
        return Event::where('is_featured', true)
            ->whereHas('featuredFees', function ($query) {
                $query->where('payment_status', 'paid')
                    ->where('featured_start_date', '<=', now())
                    ->where('featured_end_date', '>=', now());
            })
            ->get();
    }

    /**
     * Check if user can feature an event
     */
    public function canFeatureEvent(Event $event, User $user): bool
    {
        // Check if user owns the event
        if ($event->user_id !== $user->id) {
            return false;
        }

        // Check if event is published
        if (!$event->is_published) {
            return false;
        }

        // Check if featured events are enabled
        if (!config('monetization.featured_event_enabled')) {
            return false;
        }

        return true;
    }

    /**
     * Get pricing information
     */
    public function getPricingInfo(): array
    {
        return [
            'daily' => [
                'rate' => config('monetization.featured_event_rates.daily'),
                'label' => '1 Tag',
            ],
            'weekly' => [
                'rate' => config('monetization.featured_event_rates.weekly'),
                'label' => '7 Tage',
            ],
            'monthly' => [
                'rate' => config('monetization.featured_event_rates.monthly'),
                'label' => '30 Tage',
            ],
        ];
    }

    /**
     * Get user's featured event history
     */
    public function getUserFeaturedHistory(User $user)
    {
        return FeaturedEventFee::where('user_id', $user->id)
            ->with('event')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Extend featured period
     */
    public function extendFeaturedPeriod(
        Event $event,
        User $user,
        string $durationType,
        int $customDays = null
    ): FeaturedEventFee {
        $activeFee = $event->activeFeaturedFee();

        // Start from the current end date or now, whichever is later
        $startDate = $activeFee
            ? Carbon::parse($activeFee->featured_end_date)->addDay()
            : now();

        return $this->createFeaturedRequest($event, $user, $durationType, $startDate, $customDays);
    }
}

