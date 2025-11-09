<?php

namespace App\Notifications;

use App\Models\EventWaitlist;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewWaitlistEntryNotification extends Notification
{
    use Queueable;

    protected $waitlistEntry;

    public function __construct(EventWaitlist $waitlistEntry)
    {
        $this->waitlistEntry = $waitlistEntry;
    }

    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable)
    {
        $event = $this->waitlistEntry->event;

        return (new MailMessage)
            ->subject('Neue Wartelisten-Anmeldung: ' . $event->title)
            ->greeting('Hallo ' . $notifiable->first_name . ',')
            ->line('Eine neue Person hat sich für die Warteliste Ihrer Veranstaltung angemeldet.')
            ->line('**Veranstaltung:** ' . $event->title)
            ->line('**Name:** ' . $this->waitlistEntry->name)
            ->line('**E-Mail:** ' . $this->waitlistEntry->email)
            ->line('**Gewünschte Tickets:** ' . $this->waitlistEntry->quantity)
            ->action('Warteliste verwalten', route('organizer.events.waitlist.index', $event))
            ->line('Sie können die Warteliste in Ihrem Organizer-Dashboard verwalten.');
    }

    public function toArray($notifiable)
    {
        return [
            'title' => 'Neue Wartelisten-Anmeldung',
            'message' => $this->waitlistEntry->name . ' hat sich für die Warteliste von "' . $this->waitlistEntry->event->title . '" angemeldet (' . $this->waitlistEntry->quantity . ' Ticket(s))',
            'event_id' => $this->waitlistEntry->event_id,
            'event_title' => $this->waitlistEntry->event->title,
            'waitlist_id' => $this->waitlistEntry->id,
            'name' => $this->waitlistEntry->name,
            'email' => $this->waitlistEntry->email,
            'quantity' => $this->waitlistEntry->quantity,
            'url' => route('organizer.events.waitlist.index', $this->waitlistEntry->event),
        ];
    }
}

