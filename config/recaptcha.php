<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Google reCAPTCHA v3 Configuration
    |--------------------------------------------------------------------------
    |
    | Configure the reCAPTCHA v3 settings for your application.
    | By default, reCAPTCHA is only enabled in production environments.
    |
    */

    'enabled' => env('RECAPTCHA_ENABLED', env('APP_ENV') === 'production'),

    'site_key' => env('RECAPTCHA_SITE_KEY'),

    'secret_key' => env('RECAPTCHA_SECRET_KEY'),

    'verify_url' => 'https://www.google.com/recaptcha/api/siteverify',

    /*
    |--------------------------------------------------------------------------
    | Score Threshold
    |--------------------------------------------------------------------------
    |
    | reCAPTCHA v3 returns a score (1.0 is very likely a good interaction,
    | 0.0 is very likely a bot). The default threshold is 0.5.
    |
    */

    'score_threshold' => env('RECAPTCHA_SCORE_THRESHOLD', 0.5),

    /*
    |--------------------------------------------------------------------------
    | Skip for Authenticated Users
    |--------------------------------------------------------------------------
    |
    | Skip reCAPTCHA validation for authenticated users.
    |
    */

    'skip_for_authenticated' => env('RECAPTCHA_SKIP_AUTHENTICATED', true),

    /*
    |--------------------------------------------------------------------------
    | Actions
    |--------------------------------------------------------------------------
    |
    | Define specific actions for different forms. You can set different
    | score thresholds for different actions.
    |
    */

    'actions' => [
        'login' => [
            'enabled' => true,
            'threshold' => 0.5,
        ],
        'register' => [
            'enabled' => true,
            'threshold' => 0.5,
        ],
        'booking' => [
            'enabled' => true,
            'threshold' => 0.5,
        ],
        'contact' => [
            'enabled' => true,
            'threshold' => 0.3,
        ],
        'waitlist' => [
            'enabled' => true,
            'threshold' => 0.4,
        ],
        'review' => [
            'enabled' => true,
            'threshold' => 0.4,
        ],
        'access_code' => [
            'enabled' => true,
            'threshold' => 0.4,
        ],
        'password_reset' => [
            'enabled' => true,
            'threshold' => 0.5,
        ],
    ],

];

