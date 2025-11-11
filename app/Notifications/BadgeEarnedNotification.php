<?php

namespace App\Notifications;

use App\Models\Badge;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BadgeEarnedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public Badge $badge
    ) {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        $channels = ['database'];

        // Check user notification preferences
        if ($notifiable->notification_preferences['badge_earned'] ?? true) {
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
            ->subject('🏆 Neues Abzeichen erhalten!')
            ->greeting('Glückwunsch, ' . $notifiable->fullName() . '!')
            ->line('Sie haben ein neues Abzeichen verdient: **' . $this->badge->name . '**')
            ->line($this->badge->description)
            ->line('Sie haben **' . $this->badge->points . ' Punkte** erhalten.')
            ->action('Meine Abzeichen ansehen', route('dashboard'))
            ->line('Machen Sie weiter so und sammeln Sie weitere Abzeichen!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'badge_id' => $this->badge->id,
            'badge_name' => $this->badge->name,
            'badge_description' => $this->badge->description,
            'badge_points' => $this->badge->points,
            'badge_color' => $this->badge->color,
            'badge_icon' => $this->badge->icon,
            'message' => 'Sie haben das Abzeichen "' . $this->badge->name . '" erhalten!',
            'type' => 'badge_earned',
        ];
    }
}

