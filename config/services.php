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

    'winston' => [
        'key' => env('WINSTON_API_KEY', env('WINSTON_AI_TOKEN')),
        'base_uri' => env('WINSTON_API_BASE_URI', env('WINSTON_AI_BASE_URL', 'https://api.gowinston.ai/v2')),
        'endpoint' => env('WINSTON_API_ENDPOINT', env('WINSTON_AI_PLAGIARISM_PATH', 'plagiarism')),
        'language' => env('WINSTON_API_LANGUAGE', env('WINSTON_AI_LANGUAGE', 'en')),
        'country' => env('WINSTON_API_COUNTRY', env('WINSTON_AI_COUNTRY', 'us')),
        'timeout' => env('WINSTON_API_TIMEOUT', env('WINSTON_AI_TIMEOUT', 45)),
        'max_characters' => env('WINSTON_API_MAX_CHARACTERS', 20000),
    ],

];
