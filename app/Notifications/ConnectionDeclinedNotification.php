<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ConnectionDeclinedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected User $decliner;

    public function __construct(User $decliner)
    {
        $this->decliner = $decliner;
    }

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toArray($notifiable): array
    {
        return [
            'title' => 'Verbindungsanfrage abgelehnt',
            'message' => "{$this->decliner->fullName()} hat Ihre Verbindungsanfrage abgelehnt.",
            'decliner_id' => $this->decliner->id,
            'decliner_name' => $this->decliner->fullName(),
            'decliner_photo' => $this->decliner->profilePhotoUrl(),
            'action_url' => route('users.show', $this->decliner),
        ];
    }
}

