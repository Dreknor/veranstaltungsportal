<?php

namespace App\Support;

use App\Models\User;
use Illuminate\Support\Str;
use NotificationChannels\WebPush\WebPushMessage;

/**
 * Browser-Push (Web Push) zentral: Konfiguration, Einstellung der Person und Aufbau der Nachricht.
 *
 * Jede Benachrichtigung, die im Portal (Kanal "database") erscheint, kann zusätzlich als
 * Push-Nachricht zugestellt werden. Voraussetzungen: VAPID-Schlüssel gesetzt, Push in den
 * Einstellungen eingeschaltet und mindestens ein angemeldetes Gerät (Push-Abo).
 */
class PushNotifications
{
    /** Schlüssel in users.notification_preferences */
    public const PREFERENCE = 'push_enabled';

    /** Gültigkeit beim Push-Dienst: ältere Nachrichten verwirft der Dienst, statt sie nachzuliefern. */
    public const TTL_SECONDS = 86400;

    public const BODY_LIMIT = 180;

    /**
     * Sind die VAPID-Schlüssel gesetzt? Ohne sie wird nichts per Push versendet.
     */
    public static function configured(): bool
    {
        return filled(config('webpush.vapid.public_key')) && filled(config('webpush.vapid.private_key'));
    }

    public static function publicKey(): ?string
    {
        return self::configured() ? (string) config('webpush.vapid.public_key') : null;
    }

    /**
     * Hat die Person Push in den Einstellungen eingeschaltet?
     */
    public static function enabledFor(object $notifiable): bool
    {
        if (! $notifiable instanceof User) {
            return false;
        }

        return (bool) ($notifiable->notification_preferences[self::PREFERENCE] ?? false);
    }

    /**
     * Soll an diese Person per Push zugestellt werden?
     */
    public static function shouldSendTo(object $notifiable): bool
    {
        return self::configured()
            && $notifiable instanceof User
            && self::enabledFor($notifiable)
            && $notifiable->pushSubscriptions()->exists();
    }

    /**
     * Push-Nachricht aus den gespeicherten Daten einer Benachrichtigung (toDatabase/toArray).
     *
     * @param  array<string, mixed>  $data
     */
    public static function messageFromData(array $data, ?string $notificationId = null): WebPushMessage
    {
        $title = self::text($data['title'] ?? null) ?: (string) config('app.name');
        $body = Str::limit(self::text($data['message'] ?? $data['body'] ?? null), self::BODY_LIMIT, '…');
        $url = self::url($data['url'] ?? $data['action_url'] ?? null);

        return (new WebPushMessage)
            ->title($title)
            ->body($body)
            ->icon(asset('images/favicon/android-icon-192x192.png'))
            ->badge(asset('images/favicon/android-icon-96x96.png'))
            ->lang('de')
            ->data(array_filter([
                'url' => $url,
                'id' => $notificationId,
            ]))
            ->options(['TTL' => self::TTL_SECONDS]);
    }

    private static function text(mixed $value): string
    {
        if (! is_string($value)) {
            return '';
        }

        return trim(preg_replace('/\s+/u', ' ', strip_tags($value)) ?? '');
    }

    /**
     * Nur Ziele im eigenen Portal öffnen; sonst die Benachrichtigungsübersicht.
     */
    private static function url(mixed $value): string
    {
        $fallback = route('notifications.index');

        if (! is_string($value) || $value === '') {
            return $fallback;
        }

        if (str_starts_with($value, '/') && ! str_starts_with($value, '//')) {
            return url($value);
        }

        $appHost = parse_url(url('/'), PHP_URL_HOST);

        return parse_url($value, PHP_URL_HOST) === $appHost ? $value : $fallback;
    }
}
