<?php

return [
    'email' => env('COPYLEAKS_EMAIL'),
    'key' => env('COPYLEAKS_KEY'),
    'sandbox' => filter_var(env('COPYLEAKS_SANDBOX', false), FILTER_VALIDATE_BOOLEAN),
    'webhook_base' => env('COPYLEAKS_WEBHOOK_BASE'),
    'poll_timeout' => (int) env('COPYLEAKS_POLL_TIMEOUT', 60),
    'poll_interval' => (int) env('COPYLEAKS_POLL_INTERVAL', 5),
];
