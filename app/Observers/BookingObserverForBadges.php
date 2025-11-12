<?php

namespace App\Observers;

use App\Models\Booking;
use App\Services\BadgeService;

class BookingObserverForBadges
{
    protected BadgeService $badgeService;

    public function __construct(BadgeService $badgeService)
    {
        $this->badgeService = $badgeService;
    }

    /**
     * Handle the Booking "updated" event.
     */
    public function updated(Booking $booking): void
    {
        // Only check badges if booking has an associated user
        if (!$booking->user) {
            return;
        }

        // Check for badges when payment is confirmed
        if ($booking->wasChanged('payment_status') && $booking->payment_status === 'paid') {
            $this->badgeService->checkAndAwardBadges($booking->user);
        }

        // Check for badges when user checks in
        if ($booking->wasChanged('checked_in') && $booking->checked_in) {
            $this->badgeService->checkAndAwardBadges($booking->user);
        }
    }

    /**
     * Handle the Booking "deleted" event.
     */
    public function deleted(Booking $booking): void
    {
        // Optionally handle badge revocation if needed
    }

    /**
     * Handle the Booking "restored" event.
     */
    public function restored(Booking $booking): void
    {
        // Re-check badges on restoration (only if user exists)
        if ($booking->user) {
            $this->badgeService->checkAndAwardBadges($booking->user);
        }
    }
}

