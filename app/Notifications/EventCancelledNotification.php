<?php

namespace App\Notifications;

use App\Models\Booking;
use App\Models\Event;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class EventCancelledNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Event $event,
        public ?Booking $booking = null
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Veranstaltung abgesagt: ' . $this->event->title)
            ->greeting('Hallo ' . $notifiable->name . ',')
            ->line('Leider müssen wir Ihnen mitteilen, dass die folgende Veranstaltung abgesagt wurde:')
            ->line('**' . $this->event->title . '**')
            ->line('Datum: ' . $this->event->start_date->format('d.m.Y H:i') . ' Uhr')
            ->when($this->event->cancellation_reason, function ($mail) {
                return $mail->line('**Grund der Absage:**')
                    ->line($this->event->cancellation_reason);
            })
            ->when($this->booking, function ($mail) {
                return $mail->line('Ihre Buchungsnummer: ' . $this->booking->booking_number)
                    ->line('Ihre Zahlung wird automatisch erstattet.');
            })
            ->line('Wir entschuldigen uns für die Unannehmlichkeiten.')
            ->action('Weitere Veranstaltungen ansehen', route('events.index'))
            ->salutation('Mit freundlichen Grüßen, ' . config('app.name'));
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'title' => 'Veranstaltung abgesagt',
            'message' => 'Die Veranstaltung "' . $this->event->title . '" wurde abgesagt.' . ($this->event->cancellation_reason ? ' Grund: ' . $this->event->cancellation_reason : ''),
            'type' => 'event_cancelled',
            'event_id' => $this->event->id,
            'event_title' => $this->event->title,
            'event_date' => $this->event->start_date->toDateTimeString(),
            'booking_id' => $this->booking?->id,
            'booking_number' => $this->booking?->booking_number,
            'cancellation_reason' => $this->event->cancellation_reason,
            'url' => route('events.show', $this->event->slug),
        ];
    }

    public function toArray(object $notifiable): array
    {
        return $this->toDatabase($notifiable);
    }
}

