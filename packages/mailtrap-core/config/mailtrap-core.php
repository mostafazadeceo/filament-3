<?php

declare(strict_types=1);

return [
    'tables' => [
        'connections' => 'mailtrap_connections',
        'inboxes' => 'mailtrap_inboxes',
        'messages' => 'mailtrap_messages',
        'sending_domains' => 'mailtrap_sending_domains',
        'offers' => 'mailtrap_offers',
        'audiences' => 'mailtrap_audiences',
        'audience_contacts' => 'mailtrap_audience_contacts',
        'campaigns' => 'mailtrap_campaigns',
        'campaign_sends' => 'mailtrap_campaign_sends',
        'single_sends' => 'mailtrap_single_sends',
    ],

    'base_url' => env('MAILTRAP_BASE_URL', 'https://mailtrap.io/api'),
    'send_base_url' => env('MAILTRAP_SEND_BASE_URL', 'https://send.api.mailtrap.io/api'),
    'sandbox_send_base_url' => env('MAILTRAP_SANDBOX_SEND_BASE_URL', 'https://sandbox.api.mailtrap.io'),

    'http' => [
        'timeout_seconds' => (int) env('MAILTRAP_HTTP_TIMEOUT', 30),
        'retry_times' => (int) env('MAILTRAP_HTTP_RETRY_TIMES', 2),
        'retry_sleep_ms' => (int) env('MAILTRAP_HTTP_RETRY_SLEEP', 500),
    ],

    'rate_limit' => [
        'max_requests' => (int) env('MAILTRAP_RATE_MAX', 10),
        'per_seconds' => (int) env('MAILTRAP_RATE_SECONDS', 1),
    ],

    'sync' => [
        'min_seconds' => (int) env('MAILTRAP_SYNC_MIN_SECONDS', 300),
    ],

    'api' => [
        'rate_limit' => env('MAILTRAP_API_RATE_LIMIT', '60,1'),
    ],

    'logging' => [
        'enabled' => (bool) env('MAILTRAP_LOGGING_ENABLED', true),
    ],
    'fake' => (bool) env('MAILTRAP_FAKE', false),
    'fake_run_id' => env('MAILTRAP_FAKE_RUN_ID'),
];
