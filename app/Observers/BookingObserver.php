<?php

namespace App\Observers;

use App\Models\Booking;
use App\Models\EventWaitlist;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

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

        // Check if booking was cancelled
        if ($booking->wasChanged('status') && $booking->status === 'cancelled') {
            Log::info('Booking cancelled, notifying waitlist');
            $this->notifyWaitlistOnCancellation($booking);
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

