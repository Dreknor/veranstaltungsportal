<?php

namespace App\Observers;

use App\Models\Booking;
use App\Notifications\BookingStatusChangedNotification;
use App\Notifications\PaymentStatusChangedNotification;

class BookingObserver
{
    /**
     * Store original values temporarily
     */
    protected static $originalValues = [];

    /**
     * Handle the Booking "updating" event.
     * This fires before the model is saved, allowing us to track changes.
     */
    public function updating(Booking $booking)
    {
        $bookingId = $booking->id;

        // Track original values before they are changed
        if ($booking->isDirty('status')) {
            static::$originalValues[$bookingId]['status'] = $booking->getOriginal('status');
        }

        if ($booking->isDirty('payment_status')) {
            static::$originalValues[$bookingId]['payment_status'] = $booking->getOriginal('payment_status');
        }
    }

    /**
     * Handle the Booking "updated" event.
     * This fires after the model is saved.
     */
    public function updated(Booking $booking)
    {
        $bookingId = $booking->id;

        // Check if status has changed
        if (isset(static::$originalValues[$bookingId]['status'])) {
            $oldStatus = static::$originalValues[$bookingId]['status'];
            $this->notifyStatusChange($booking, $oldStatus, $booking->status);
            unset(static::$originalValues[$bookingId]['status']);
        }

        // Check if payment status has changed
        if (isset(static::$originalValues[$bookingId]['payment_status'])) {
            $oldPaymentStatus = static::$originalValues[$bookingId]['payment_status'];
            $this->notifyPaymentStatusChange($booking, $oldPaymentStatus, $booking->payment_status);
            unset(static::$originalValues[$bookingId]['payment_status']);
        }

        // Clean up if no more values stored for this booking
        if (isset(static::$originalValues[$bookingId]) && empty(static::$originalValues[$bookingId])) {
            unset(static::$originalValues[$bookingId]);
        }
    }

    /**
     * Send notification for status change
     */
    protected function notifyStatusChange(Booking $booking, string $oldStatus, string $newStatus)
    {
        // Load relationships if not loaded
        if (!$booking->relationLoaded('user')) {
            $booking->load('user');
        }
        if (!$booking->relationLoaded('event')) {
            $booking->load('event');
        }

        // Notify the user if exists
        if ($booking->user) {
            $booking->user->notify(new BookingStatusChangedNotification($booking, $oldStatus, $newStatus));
        }

        // Also send email to customer_email if different from user email or no user
        if (!$booking->user || ($booking->customer_email && $booking->customer_email !== $booking->user->email)) {
            \Illuminate\Support\Facades\Notification::route('mail', $booking->customer_email)
                ->notify(new BookingStatusChangedNotification($booking, $oldStatus, $newStatus));
        }
    }

    /**
     * Send notification for payment status change
     */
    protected function notifyPaymentStatusChange(Booking $booking, string $oldPaymentStatus, string $newPaymentStatus)
    {
        // Load relationships if not loaded
        if (!$booking->relationLoaded('user')) {
            $booking->load('user');
        }
        if (!$booking->relationLoaded('event')) {
            $booking->load('event');
        }

        // Notify the user if exists
        if ($booking->user) {
            $booking->user->notify(new PaymentStatusChangedNotification($booking, $oldPaymentStatus, $newPaymentStatus));
        }

        // Also send email to customer_email if different from user email or no user
        if (!$booking->user || ($booking->customer_email && $booking->customer_email !== $booking->user->email)) {
            \Illuminate\Support\Facades\Notification::route('mail', $booking->customer_email)
                ->notify(new PaymentStatusChangedNotification($booking, $oldPaymentStatus, $newPaymentStatus));
        }
    }
}

