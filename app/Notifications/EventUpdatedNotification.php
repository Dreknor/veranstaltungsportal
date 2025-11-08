<?php

namespace App\Notifications;

use App\Models\Event;
use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class EventUpdatedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $event;
    protected $booking;
    protected $changes;

    /**
     * Create a new notification instance.
     */
    public function __construct(Event $event, Booking $booking, array $changes = [])
    {
        $this->event = $event;
        $this->booking = $booking;
        $this->changes = $changes;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        $channels = ['database'];

        // Check user preferences
        $preferences = $notifiable->notification_preferences ?? [];
        if ($preferences['email_event_updated'] ?? true) {
            $channels[] = 'mail';
        }

        return $channels;
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $mailMessage = (new MailMessage)
            ->subject('Veranstaltung aktualisiert: ' . $this->event->title)
            ->greeting('Hallo ' . $notifiable->name . ',')
            ->line('Eine Veranstaltung, für die Sie gebucht haben, wurde aktualisiert:')
            ->line('**' . $this->event->title . '**');

        if (!empty($this->changes)) {
            $mailMessage->line('Folgende Änderungen wurden vorgenommen:');
            foreach ($this->changes as $field => $change) {
                $mailMessage->line("- {$field}: {$change}");
            }
        }

        $mailMessage
            ->line('Datum: ' . $this->event->start_date->format('d.m.Y H:i') . ' Uhr')
            ->line('Ort: ' . $this->event->venue_name . ', ' . $this->event->venue_city)
            ->action('Veranstaltung ansehen', route('events.show', $this->event->slug))
            ->line('Bei Fragen kontaktieren Sie bitte den Veranstalter.');

        return $mailMessage;
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Veranstaltung aktualisiert',
            'message' => 'Die Veranstaltung "' . $this->event->title . '" wurde aktualisiert.',
            'event_id' => $this->event->id,
            'event_title' => $this->event->title,
            'event_slug' => $this->event->slug,
            'booking_id' => $this->booking->id,
            'changes' => $this->changes,
            'url' => route('events.show', $this->event->slug),
        ];
    }
}
