<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ConnectionRequestNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public User $follower
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
        if ($preferences['connection_requests'] ?? true) {
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
            ->subject('Neue Verbindungsanfrage')
            ->greeting('Hallo ' . $notifiable->fullName() . '!')
            ->line($this->follower->fullName() . ' möchte sich mit Ihnen vernetzen.')
            ->line('Vernetzen Sie sich mit anderen Teilnehmern und Organisatoren, um gemeinsam zu lernen und sich auszutauschen.')
            ->action('Anfrage ansehen', url('/connections/requests'))
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
            'type' => 'connection_request',
            'follower_id' => $this->follower->id,
            'follower_name' => $this->follower->fullName(),
            'follower_photo' => $this->follower->profilePhotoUrl(),
            'message' => $this->follower->fullName() . ' möchte sich mit Ihnen vernetzen.',
            'action_url' => url('/connections/requests'),
        ];
    }
}

