<?php

declare(strict_types=1);

return [
    'tables' => [
        'job_logs' => 'providers_core_job_logs',
    ],

    'queue' => env('PROVIDERS_CORE_QUEUE', 'providers'),

    'retry' => [
        'tries' => 3,
        'backoff_seconds' => [10, 30, 60],
    ],
    'job_timeout_seconds' => (int) env('PROVIDERS_CORE_JOB_TIMEOUT', 600),

    'logging' => [
        'store_payloads' => true,
        'store_results' => true,
    ],
];
