<?php

return [
    'mode'    => env('PAYPAL_MODE', 'sandbox'), // Can only be 'sandbox' or 'live'

    'sandbox' => [
        'client_id'         => env('PAYPAL_SANDBOX_CLIENT_ID', ''),
        'client_secret'     => env('PAYPAL_SANDBOX_CLIENT_SECRET', ''),
        'app_id'            => 'APP-80W284485P519543T',
    ],

    'live' => [
        'client_id'         => env('PAYPAL_LIVE_CLIENT_ID', ''),
        'client_secret'     => env('PAYPAL_LIVE_CLIENT_SECRET', ''),
        'app_id'            => '',
    ],

    'payment_action' => 'CAPTURE', // Can only be 'AUTHORIZE' or 'CAPTURE'

    'currency'       => env('CURRENCY', 'EUR'),

    'notify_url'     => env('PAYPAL_NOTIFY_URL', ''),

    'locale'         => env('APP_LOCALE', 'de_DE'),

    'validate_ssl'   => env('PAYPAL_VALIDATE_SSL', true),

    'webhook_id'     => env('PAYPAL_WEBHOOK_ID', ''),
];
