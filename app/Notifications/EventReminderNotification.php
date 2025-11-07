<?php

namespace App\Notifications;

use App\Models\Event;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class EventReminderNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $event;
    protected $booking;

    public function __construct($event, $booking)
    {
        $this->event = $event;
        $this->booking = $booking;
    }

    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Erinnerung: ' . $this->event->title . ' beginnt bald!')
            ->greeting('Hallo ' . $this->booking->customer_name . ',')
            ->line('Dies ist eine Erinnerung an Ihre bevorstehende Veranstaltung.')
            ->line('Event: ' . $this->event->title)
            ->line('Datum: ' . $this->event->start_date->format('d.m.Y H:i'))
            ->line('Ort: ' . $this->event->venue_name . ', ' . $this->event->venue_city)
            ->action('Event-Details ansehen', route('events.show', $this->event->slug))
            ->line('Wir freuen uns auf Sie!');
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

