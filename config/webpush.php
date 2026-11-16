<?php

use NotificationChannels\WebPush\PushSubscription;

/*
| Browser-Push (laravel-notification-channels/webpush)
|
| Schlüssel einmalig erzeugen mit `php artisan webpush:vapid` (schreibt VAPID_PUBLIC_KEY und
| VAPID_PRIVATE_KEY in die .env) und danach nicht mehr ändern – sonst verlieren alle angemeldeten
| Geräte ihr Abo. Ohne Schlüssel bleibt Push abgeschaltet; Benachrichtigungen gehen weiter ins
| Portal und per E-Mail.
*/

return [

    'vapid' => [
        // Kontakt für die Push-Dienste: "mailto:…" oder https-Adresse des Portals
        'subject' => env('VAPID_SUBJECT'),
        'public_key' => env('VAPID_PUBLIC_KEY'),
        'private_key' => env('VAPID_PRIVATE_KEY'),
        'pem_file' => env('VAPID_PEM_FILE'),
    ],

    'model' => PushSubscription::class,

    'table_name' => env('WEBPUSH_DB_TABLE', 'push_subscriptions'),

    // null = Standardverbindung der Anwendung
    'database_connection' => env('WEBPUSH_DB_CONNECTION'),

    'client_options' => [],

    'automatic_padding' => env('WEBPUSH_AUTOMATIC_PADDING', true),

];
