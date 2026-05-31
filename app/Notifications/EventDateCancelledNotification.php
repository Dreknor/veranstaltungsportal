<?php

namespace App\Notifications;

use App\Models\Booking;
use App\Models\Event;
use App\Models\EventDate;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class EventDateCancelledNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Event $event,
        public EventDate $eventDate,
        public ?Booking $booking = null
    ) {}

    public function via(object $notifiable): array
    {
        return $notifiable instanceof \App\Models\User
            ? ['mail', 'database']
            : ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $mail = (new MailMessage)
            ->subject('Termin abgesagt: ' . $this->event->title)
            ->greeting('Hallo ' . ($notifiable->name ?? '') . ',')
            ->line('leider müssen wir Ihnen mitteilen, dass ein Termin der folgenden Veranstaltung abgesagt wurde:')
            ->line('**' . $this->event->title . '**')
            ->line('Abgesagter Termin: ' . $this->eventDate->start_date->format('d.m.Y H:i') . ' Uhr');

        if ($this->eventDate->cancellation_reason) {
            $mail->line('**Grund der Absage:**')
                ->line($this->eventDate->cancellation_reason);
        }

        if ($this->booking) {
            $mail->line('Ihre Buchungsnummer: ' . $this->booking->booking_number);
        }

        return $mail
            ->line('Die Veranstaltung findet an den übrigen Terminen weiterhin statt. Bitte prüfen Sie die verfügbaren Termine.')
            ->action('Veranstaltung ansehen', route('events.show', $this->event->slug))
            ->salutation('Mit freundlichen Grüßen, ' . config('app.name'));
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'title' => 'Termin abgesagt',
            'message' => 'Ein Termin der Veranstaltung "' . $this->event->title . '" am '
                . $this->eventDate->start_date->format('d.m.Y H:i') . ' Uhr wurde abgesagt.'
                . ($this->eventDate->cancellation_reason ? ' Grund: ' . $this->eventDate->cancellation_reason : ''),
            'type' => 'event_date_cancelled',
            'event_id' => $this->event->id,
            'event_title' => $this->event->title,
            'event_date_id' => $this->eventDate->id,
            'event_date' => $this->eventDate->start_date->toDateTimeString(),
            'booking_id' => $this->booking?->id,
            'booking_number' => $this->booking?->booking_number,
            'cancellation_reason' => $this->eventDate->cancellation_reason,
            'url' => route('events.show', $this->event->slug),
        ];
    }

    public function toArray(object $notifiable): array
    {
        return $this->toDatabase($notifiable);
    }
}

