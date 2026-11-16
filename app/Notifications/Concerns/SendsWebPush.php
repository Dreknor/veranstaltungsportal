<?php

namespace App\Notifications\Concerns;

use App\Notifications\Channels\SafeWebPushChannel;
use App\Support\PushNotifications;
use NotificationChannels\WebPush\WebPushMessage;

/**
 * Stellt eine Benachrichtigung, die im Portal erscheint (Kanal "database"), zusätzlich per
 * Browser-Push zu – sofern die Person Push eingeschaltet und ein Gerät angemeldet hat.
 * Titel, Text und Ziel kommen aus toDatabase()/toArray(); die Benachrichtigung braucht nichts weiter.
 */
trait SendsWebPush
{
    /**
     * @param  array<int, string>  $channels
     * @return array<int, string>
     */
    protected function withWebPush(array $channels, object $notifiable): array
    {
        if (in_array('database', $channels, true) && PushNotifications::shouldSendTo($notifiable)) {
            $channels[] = SafeWebPushChannel::class;
        }

        return $channels;
    }

    public function toWebPush(object $notifiable, mixed $notification = null): WebPushMessage
    {
        // Dieselben Daten wie im Portal: toDatabase() hat Vorrang vor toArray()
        $method = collect(['toDatabase', 'toArray'])->first(fn (string $name) => method_exists($this, $name));
        $data = $method !== null ? $this->{$method}($notifiable) : [];

        return PushNotifications::messageFromData(is_array($data) ? $data : [], $this->id);
    }
}
