<?php

declare(strict_types=1);

return [
    'provider' => [
        'default' => env('SMS_BULK_PROVIDER_DEFAULT', 'ippanel_edge'),
        'ippanel_edge' => [
            'base_url' => env('IPPANEL_EDGE_BASE_URL', 'https://edge.ippanel.com/v1'),
            'token' => env('IPPANEL_EDGE_TOKEN', '__PUT_EDGE_TOKEN_HERE__'),
            'default_sender' => env('IPPANEL_EDGE_DEFAULT_SENDER', '__PUT_SENDER_NUMBER_HERE__'),
            'test_mobile' => env('IPPANEL_EDGE_TEST_MOBILE', '__PUT_TEST_MOBILE_HERE__'),
            'timeout_seconds' => (int) env('IPPANEL_EDGE_TIMEOUT_SECONDS', 15),
            'retry_times' => (int) env('IPPANEL_EDGE_RETRY_TIMES', 3),
            'retry_sleep_ms' => (int) env('IPPANEL_EDGE_RETRY_SLEEP_MS', 250),
        ],
    ],
    'api' => [
        'rate_limit' => env('SMS_BULK_API_RATE_LIMIT', '60,1'),
        'prefix' => 'api/v1/sms-bulk',
    ],
    'queue' => [
        'chunk_size' => (int) env('SMS_BULK_QUEUE_CHUNK_SIZE', 500),
    ],
    'compliance' => [
        'stop_keywords' => [
            'fa' => ['لغو', 'توقف', 'قطع', 'STOP'],
            'en' => ['STOP', 'UNSUBSCRIBE'],
            'ar' => ['إلغاء', 'توقف'],
        ],
        'forbidden_words' => [],
    ],
    'i18n' => [
        'default_locale' => 'fa',
        'fallback_locale' => 'en',
        'enabled_locales' => ['fa', 'en', 'ar'],
    ],
    'table_prefix' => 'sms_bulk_',
];
