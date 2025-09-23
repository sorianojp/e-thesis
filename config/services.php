<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'step' => [
        'base_url' => env('STEP_API_BASE', 'https://udd.steps.com.ph'),
    ],

    'copyleaks' => [
        'email' => env('COPYLEAKS_EMAIL'),
        'api_key' => env('COPYLEAKS_API_KEY'),
        'identity_url' => env('COPYLEAKS_IDENTITY_URL', 'https://id.copyleaks.com'),
        'api_url' => env('COPYLEAKS_API_URL', 'https://api.copyleaks.com'),
        'timeout' => env('COPYLEAKS_TIMEOUT', 30),
        'sandbox' => env('COPYLEAKS_SANDBOX', false),
        'poll_attempts' => env('COPYLEAKS_POLL_ATTEMPTS', 5),
        'poll_interval' => env('COPYLEAKS_POLL_INTERVAL', 5),
        'webhook_base' => env('COPYLEAKS_WEBHOOK_BASE', env('APP_URL')),
        'export_formats' => array_filter(array_map('trim', explode(',', env('COPYLEAKS_EXPORT_FORMATS', 'pdf')))),
    ],

];
