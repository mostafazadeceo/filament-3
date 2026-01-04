<?php

declare(strict_types=1);

return [
    'enabled' => env('OBSERVABILITY_ENABLED', true),

    'correlation' => [
        'header' => env('OBSERVABILITY_CORRELATION_HEADER', 'X-Correlation-Id'),
        'context_key' => 'correlation_id',
    ],
];
