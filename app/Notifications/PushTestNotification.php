<?php

namespace App\Notifications;

use App\Notifications\Channels\SafeWebPushChannel;
use App\Support\PushNotifications;
use Illuminate\Notifications\Notification;
use NotificationChannels\WebPush\WebPushMessage;

/**
 * Testnachricht aus Einstellungen › Benachrichtigungen: nur per Push, ohne Eintrag im Portal
 * und ohne E-Mail.
 */
class PushTestNotification extends Notification
{
    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return [SafeWebPushChannel::class];
    }

    public function toWebPush(object $notifiable, mixed $notification = null): WebPushMessage
    {
        return PushNotifications::messageFromData([
            'title' => 'Testnachricht',
            'message' => 'Push-Benachrichtigungen funktionieren auf diesem Gerät.',
            'url' => route('settings.notifications.edit'),
        ], $this->id);
    }
}
