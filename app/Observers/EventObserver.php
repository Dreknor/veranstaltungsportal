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
        // Generate slug if not already set
        if (empty($event->slug) && !empty($event->title)) {
            $event->slug = \Illuminate\Support\Str::slug($event->title) . '-' . \Illuminate\Support\Str::random(6);
        }

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
     *
     * Informiert alle bestätigten Buchungen (registrierte Nutzer UND Gäste) über wichtige Änderungen.
     * Neue Online-Zugangsdaten werden nur an Buchungen mit freigeschaltetem Zugang (bestätigt + bezahlt) versendet.
     */
    public function updated(Event $event): void
    {
        // Nur veröffentlichte, nicht abgesagte Veranstaltungen
        if (!$event->is_published || $event->is_cancelled) {
            return;
        }

        $importantFields = [
            'title' => 'Titel',
            'start_date' => 'Beginn',
            'end_date' => 'Ende',
            'venue_name' => 'Veranstaltungsort',
            'venue_address' => 'Adresse',
            'venue_postal_code' => 'PLZ',
            'venue_city' => 'Stadt',
        ];

        $changes = [];
        foreach ($importantFields as $field => $label) {
            if (!$event->wasChanged($field)) {
                continue;
            }

            $oldValue = $event->getOriginal($field);
            $newValue = $event->$field;

            if (in_array($field, ['start_date', 'end_date'])) {
                $oldValue = $oldValue ? \Carbon\Carbon::parse($oldValue)->format('d.m.Y H:i') . ' Uhr' : '–';
                $newValue = $newValue ? \Carbon\Carbon::parse($newValue)->format('d.m.Y H:i') . ' Uhr' : '–';
                if ($oldValue === $newValue) {
                    continue;
                }
            }

            $changes[$label] = ($oldValue ?: '–') . ' → ' . ($newValue ?: '–');
        }

        $accessChanged = $event->requiresOnlineInfo()
            && ($event->wasChanged('online_url') || $event->wasChanged('online_access_code'));

        if (empty($changes) && !$accessChanged) {
            return;
        }

        $bookings = $event->bookings()
            ->whereIn('status', ['confirmed'])
            ->with('user')
            ->get();

        foreach ($bookings as $booking) {
            $hasAccess = $accessChanged && $booking->canAccessOnlineContent();

            // Nur Zugangsdaten geändert, aber Buchung hat (noch) keinen Zugang → nichts senden
            if (empty($changes) && !$hasAccess) {
                continue;
            }

            $notification = new EventUpdatedNotification($event, $booking, $changes, $hasAccess);

            try {
                if ($booking->user) {
                    $booking->user->notify($notification);
                } else {
                    \Illuminate\Support\Facades\Notification::route('mail', $booking->customer_email)
                        ->notify($notification);
                }
            } catch (\Throwable $e) {
                \Illuminate\Support\Facades\Log::error('Änderungsbenachrichtigung fehlgeschlagen', [
                    'booking_id' => $booking->id,
                    'error' => $e->getMessage(),
                ]);
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
