<?php

namespace App\Observers;

use App\Models\Booking;
use App\Notifications\BookingStatusChangedNotification;
use App\Notifications\PaymentStatusChangedNotification;
use Illuminate\Support\Facades\Log;

class BookingObserver
{
    /**
     * Handle the Booking "updated" event.
     *
     * - In-App-Hinweise zu Status-/Zahlungsänderungen für registrierte Nutzer
     *   (E-Mails verschickt ausschließlich der BookingWorkflowService, damit nichts doppelt ankommt)
     * - Warteliste informieren, wenn durch eine Stornierung Plätze frei werden (einzige Stelle dafür)
     */
    public function updated(Booking $booking)
    {
        // Send notification when booking status changes
        if ($booking->wasChanged('status')) {
            $oldStatus = (string) $booking->getOriginal('status');
            $newStatus = $booking->status;

            $this->sendBookingStatusNotification($booking, $oldStatus, $newStatus);

            // Also check if booking was cancelled for waitlist notification
            if ($newStatus === 'cancelled') {
                $this->notifyWaitlistOnCancellation($booking);
            }
        }

        // Send notification when payment status changes
        if ($booking->wasChanged('payment_status')) {
            $oldPaymentStatus = (string) $booking->getOriginal('payment_status');
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
        // Gäste haben keinen In-App-Bereich; sie erhalten die jeweilige Prozess-E-Mail
        $booking->user?->notify(new BookingStatusChangedNotification($booking, $oldStatus, $newStatus));
    }

    /**
     * Send payment status change notification
     */
    protected function sendPaymentStatusNotification(Booking $booking, string $oldPaymentStatus, string $newPaymentStatus)
    {
        $booking->user?->notify(new PaymentStatusChangedNotification($booking, $oldPaymentStatus, $newPaymentStatus));
    }

    /**
     * Notify waitlist when tickets become available
     */
    protected function notifyWaitlistOnCancellation(Booking $booking)
    {
        $event = $booking->event;

        // Keine Warteliste bei abgesagten oder bereits begonnenen Veranstaltungen
        if (!$event || $event->is_cancelled || $event->start_date?->isPast()) {
            return;
        }

        $freedTickets = (int) $booking->items->sum('quantity');
        $notified = app(\App\Services\WaitlistService::class)->offerSeats($event, $freedTickets);

        if ($notified > 0) {
            Log::info('Warteliste benachrichtigt', [
                'event_id' => $event->id,
                'freed_tickets' => $freedTickets,
                'notified' => $notified,
            ]);
        }
    }
}

