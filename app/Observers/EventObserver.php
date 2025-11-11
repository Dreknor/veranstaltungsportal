<?php

namespace App\Observers;

use App\Models\Event;
use App\Models\User;
use App\Notifications\EventUpdatedNotification;
use App\Notifications\NewEventInCategoryNotification;

class EventObserver
{
    /**
     * Handle the Event "creating" event.
     */
    public function creating(Event $event): void
    {
        // Calculate duration before creating
        if ($event->start_date && $event->end_date && !$event->duration) {
            $event->calculateDuration();
        }
    }

    /**
     * Handle the Event "created" event.
     */
    public function created(Event $event): void
    {
        // Only notify if event is published
        if (!$event->is_published) {
            return;
        }

        // Notify users interested in this category
        if ($event->event_category_id) {
            User::whereJsonContains('interested_category_ids', $event->event_category_id)
                ->each(function ($user) use ($event) {
                    $user->notify(new NewEventInCategoryNotification($event));
                });
        }
    }

    /**
     * Handle the Event "updating" event.
     */
    public function updating(Event $event): void
    {
        // Recalculate duration if start_date or end_date changed
        if ($event->isDirty(['start_date', 'end_date'])) {
            $event->calculateDuration();
        }
    }

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
