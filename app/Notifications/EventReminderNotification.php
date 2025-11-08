<?php

namespace App\Notifications;

use App\Mail\EventReminderMail;
use App\Models\Booking;
use App\Models\Event;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class EventReminderNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        protected Event $event,
        protected Booking $booking,
    ) {}

    public function via($notifiable)
    {
        $channels = ['database'];

        // Check if user wants email notifications
        $preferences = $notifiable->notification_preferences ?? [];
        if (!isset($preferences['email_event_reminder']) || $preferences['email_event_reminder']) {
            $channels[] = 'mail';
        }

        return $channels;
    }

    public function toMail($notifiable)
    {
        return new EventReminderMail($this->event, $this->booking);
    }

    public function toArray($notifiable)
    {
        return [
            'title' => 'Event-Erinnerung',
            'message' => 'Erinnerung: "' . $this->event->title . '" beginnt am ' . $this->event->start_date->format('d.m.Y H:i'),
            'event_id' => $this->event->id,
            'booking_id' => $this->booking->id,
            'url' => route('events.show', $this->event->slug),
        ];
    }
}

