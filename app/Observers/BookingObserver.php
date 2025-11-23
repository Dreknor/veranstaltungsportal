<?php

namespace App\Observers;

use App\Models\Booking;
use App\Models\EventWaitlist;
use App\Notifications\BookingStatusChangedNotification;
use App\Notifications\PaymentStatusChangedNotification;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class BookingObserver
{
    /**
     * Handle the Booking "updated" event.
     * Notify waitlist when booking is cancelled
     */
    public function updated(Booking $booking)
    {
        Log::info('BookingObserver::updated called', [
            'booking_id' => $booking->id,
            'status' => $booking->status,
            'was_changed' => $booking->wasChanged('status'),
            'original_status' => $booking->getOriginal('status')
        ]);

        // Send notification when booking status changes
        if ($booking->wasChanged('status')) {
            $oldStatus = $booking->getOriginal('status');
            $newStatus = $booking->status;

            $this->sendBookingStatusNotification($booking, $oldStatus, $newStatus);

            // Also check if booking was cancelled for waitlist notification
            if ($newStatus === 'cancelled') {
                Log::info('Booking cancelled, notifying waitlist');
                $this->notifyWaitlistOnCancellation($booking);
            }
        }

        // Send notification when payment status changes
        if ($booking->wasChanged('payment_status')) {
            $oldPaymentStatus = $booking->getOriginal('payment_status');
            $newPaymentStatus = $booking->payment_status;

            $this->sendPaymentStatusNotification($booking, $oldPaymentStatus, $newPaymentStatus);
        }
    }

    /**
     * Handle the Booking "deleted" event.
     */
    public function deleted(Booking $booking)
    {
        // Only notify if booking was confirmed/completed
        if (in_array($booking->status, ['confirmed', 'completed'])) {
            $this->notifyWaitlistOnCancellation($booking);
        }
    }

    /**
     * Send booking status change notification
     */
    protected function sendBookingStatusNotification(Booking $booking, string $oldStatus, string $newStatus)
    {
        $notification = new BookingStatusChangedNotification($booking, $oldStatus, $newStatus);

        // If booking has a user, notify them
        if ($booking->user) {
            $booking->user->notify($notification);
        } else {
            // For guest bookings, send via on-demand notification
            Notification::route('mail', $booking->customer_email)
                ->notify($notification);
        }
    }

    /**
     * Send payment status change notification
     */
    protected function sendPaymentStatusNotification(Booking $booking, string $oldPaymentStatus, string $newPaymentStatus)
    {
        $notification = new PaymentStatusChangedNotification($booking, $oldPaymentStatus, $newPaymentStatus);

        // If booking has a user, notify them
        if ($booking->user) {
            $booking->user->notify($notification);
        } else {
            // For guest bookings, send via on-demand notification
            Notification::route('mail', $booking->customer_email)
                ->notify($notification);
        }
    }

    /**
     * Notify waitlist when tickets become available
     */
    protected function notifyWaitlistOnCancellation(Booking $booking)
    {
        $event = $booking->event;

        // Calculate freed tickets
        $freedTickets = $booking->items->sum('quantity');

        Log::info('Processing waitlist notification', [
            'event_id' => $event->id,
            'freed_tickets' => $freedTickets
        ]);

        if ($freedTickets > 0) {
            // Find waiting entries that could fit
            $waitingEntries = EventWaitlist::where('event_id', $event->id)
                ->waiting()
                ->notExpired()
                ->where('quantity', '<=', $freedTickets)
                ->orderBy('created_at')
                ->limit(5)
                ->get();

            Log::info('Found waitlist entries', [
                'count' => $waitingEntries->count()
            ]);

            $remainingTickets = $freedTickets;
            $notifiedCount = 0;

            foreach ($waitingEntries as $entry) {
                if ($remainingTickets >= $entry->quantity) {
                    $entry->markAsNotified();

                    // Send notification
                    try {
                        Mail::to($entry->email)->send(new \App\Mail\WaitlistTicketAvailable($entry));
                        $remainingTickets -= $entry->quantity;
                        $notifiedCount++;
                    } catch (\Exception $e) {
                        Log::error('Failed to send waitlist notification', [
                            'waitlist_id' => $entry->id,
                            'error' => $e->getMessage()
                        ]);
                    }
                }

                if ($remainingTickets <= 0) {
                    break;
                }
            }

            if ($notifiedCount > 0) {
                Log::info("Waitlist notifications sent", [
                    'event_id' => $event->id,
                    'freed_tickets' => $freedTickets,
                    'notified' => $notifiedCount
                ]);
            }
        }
    }
}

