<?php

namespace App\Observers;

use App\Models\Event;
use App\Notifications\EventUpdatedNotification;

class EventObserver
{
    /**
     * Handle the Event "updated" event.
     */
    public function updated(Event $event): void
    {
        // Only notify if event is published
        if (!$event->is_published) {
            return;
        }

        // Track important changes
        $importantFields = [
            'title' => 'Titel',
            'start_date' => 'Startdatum',
            'end_date' => 'Enddatum',
            'venue_name' => 'Veranstaltungsort',
            'venue_address' => 'Adresse',
            'venue_city' => 'Stadt',
        ];

        $changes = [];
        foreach ($importantFields as $field => $label) {
            if ($event->isDirty($field)) {
                $oldValue = $event->getOriginal($field);
                $newValue = $event->$field;

                // Format dates
                if (in_array($field, ['start_date', 'end_date'])) {
                    $oldValue = \Carbon\Carbon::parse($oldValue)->format('d.m.Y H:i');
                    $newValue = \Carbon\Carbon::parse($newValue)->format('d.m.Y H:i');
                }

                $changes[$label] = "{$oldValue} → {$newValue}";
            }
        }

        // Only send notifications if there are important changes
        if (empty($changes)) {
            return;
        }

        // Notify all users with confirmed bookings
        $bookings = $event->bookings()
            ->where('status', 'confirmed')
            ->with('user')
            ->get();

        foreach ($bookings as $booking) {
            if ($booking->user) {
                $booking->user->notify(new EventUpdatedNotification($event, $booking, $changes));
            }
        }
    }

    /**
     * Handle the Event "deleted" event.
     */
    public function deleted(Event $event): void
    {
        // Could notify users about event cancellation
    }

    /**
     * Handle the Event "restored" event.
     */
    public function restored(Event $event): void
    {
        //
    }

    /**
     * Handle the Event "force deleted" event.
     */
    public function forceDeleted(Event $event): void
    {
        //
    }
}
