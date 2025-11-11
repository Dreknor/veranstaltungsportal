<?php

namespace App\Notifications;

use App\Models\Event;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewEventInCategoryNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public Event $event
    ) {}

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        $channels = ['database'];

        // Check user's notification preferences
        $preferences = $notifiable->notification_preferences ?? [];
        if (isset($preferences['new_events_in_categories']) && $preferences['new_events_in_categories']) {
            $channels[] = 'mail';
        }

        return $channels;
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Neue Veranstaltung: ' . $this->event->title)
            ->greeting('Hallo ' . $notifiable->fullName() . ',')
            ->line('Es gibt eine neue Veranstaltung in einer Ihrer Lieblingskategorien!')
            ->line('**' . $this->event->title . '**')
            ->line($this->event->category?->name)
            ->line('📅 ' . $this->event->start_date->format('d.m.Y H:i') . ' Uhr')
            ->line('📍 ' . ($this->event->isOnline() ? 'Online-Veranstaltung' : $this->event->venue_name))
            ->action('Veranstaltung ansehen', route('events.show', $this->event))
            ->line('Verpassen Sie nicht diese spannende Gelegenheit!')
            ->salutation('Viele Grüße, Ihr Bildungsportal-Team');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'event_id' => $this->event->id,
            'event_title' => $this->event->title,
            'event_category' => $this->event->category?->name,
            'event_date' => $this->event->start_date->format('d.m.Y H:i'),
            'message' => 'Neue Veranstaltung: ' . $this->event->title . ' in ' . $this->event->category?->name,
        ];
    }
}

