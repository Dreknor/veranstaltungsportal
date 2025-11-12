<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewOrganizerRegisteredNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public User $organizer
    ) {}

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Neuer Organisator registriert: ' . $this->organizer->fullName())
            ->greeting('Hallo ' . $notifiable->fullName() . ',')
            ->line('Ein neuer Organisator hat sich auf der Plattform registriert.')
            ->line('**Name:** ' . $this->organizer->fullName())
            ->line('**E-Mail:** ' . $this->organizer->email)
            ->line('**Organisation:** ' . ($this->organizer->organization_name ?? 'Keine Angabe'))
            ->line('**Registriert am:** ' . $this->organizer->created_at->format('d.m.Y H:i') . ' Uhr')
            ->action('Organisator ansehen', route('admin.users.edit', $this->organizer))
            ->line('Bitte überprüfen Sie den neuen Organisator und nehmen Sie bei Bedarf Kontakt auf.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Neuer Organisator registriert',
            'message' => $this->organizer->fullName() . ' hat sich als Organisator auf der Plattform registriert',
            'organizer_id' => $this->organizer->id,
            'organizer_name' => $this->organizer->fullName(),
            'organizer_email' => $this->organizer->email,
            'organization_name' => $this->organizer->organization_name,
            'registered_at' => $this->organizer->created_at->toISOString(),
            'url' => route('admin.users.edit', $this->organizer),
        ];
    }
}

