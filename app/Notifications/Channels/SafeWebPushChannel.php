<?php

namespace App\Notifications\Channels;

use Illuminate\Contracts\Container\Container;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;
use NotificationChannels\WebPush\WebPushChannel;
use Throwable;

/**
 * Hülle um den Web-Push-Kanal des Pakets: Fehler beim Push-Versand (Dienst nicht erreichbar,
 * ungültige Schlüssel) werden protokolliert, unterbrechen aber weder die Anfrage noch die
 * Zustellung über die übrigen Kanäle (E-Mail, Portal).
 */
class SafeWebPushChannel
{
    public function __construct(private Container $container) {}

    /**
     * @return array<int, mixed>
     */
    public function send(mixed $notifiable, Notification $notification): array
    {
        // minishlink/web-push meldet Hinweise (z. B. "GMP oder BCMath empfohlen") per trigger_error;
        // Laravel würde daraus eine Exception machen und den Versand abbrechen. Nur protokollieren.
        set_error_handler(function (int $level, string $message): bool {
            $level === E_USER_WARNING
                ? Log::warning('Web Push: '.$message)
                : Log::debug('Web Push: '.$message);

            return true;
        }, E_USER_NOTICE | E_USER_WARNING);

        try {
            return $this->container->make(WebPushChannel::class)->send($notifiable, $notification);
        } catch (Throwable $e) {
            report($e);

            return [];
        } finally {
            restore_error_handler();
        }
    }
}
