<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ConnectionAcceptedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public User $acceptedBy
    ) {}

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
        if ($preferences['connection_accepted'] ?? true) {
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
            ->subject('Verbindungsanfrage akzeptiert')
            ->greeting('Hallo ' . $notifiable->fullName() . '!')
            ->line($this->acceptedBy->fullName() . ' hat Ihre Verbindungsanfrage akzeptiert.')
            ->line('Sie sind jetzt miteinander verbunden und können sich gegenseitig folgen.')
            ->action('Profil ansehen', url('/users/' . $this->acceptedBy->id))
            ->line('Vielen Dank für Ihre Teilnahme am Bildungsportal!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'connection_accepted',
            'user_id' => $this->acceptedBy->id,
            'user_name' => $this->acceptedBy->fullName(),
            'user_photo' => $this->acceptedBy->profilePhotoUrl(),
            'message' => $this->acceptedBy->fullName() . ' hat Ihre Verbindungsanfrage akzeptiert.',
            'action_url' => url('/users/' . $this->acceptedBy->id),
        ];
    }
}

