<?php

declare(strict_types=1);

return [
    'base_url' => env('ESIM_GO_BASE_URL', 'https://api.esim-go.com/v2.5'),
    'sandbox_base_url' => env('ESIM_GO_SANDBOX_BASE_URL', 'https://api.esim-go.com/v2.5'),
    'api_key_header' => env('ESIM_GO_API_KEY_HEADER', 'X-API-Key'),
    'signature_headers' => [
        'X-ESIMGO-SIGNATURE',
        'X-ESIM-GO-SIGNATURE',
        'X-Signature',
        'X-Hub-Signature-256',
    ],
    'rate_limit' => [
        'max_requests' => 10,
        'per_seconds' => 1,
    ],
    'retry' => [
        'tries' => 3,
        'base_delay_ms' => 500,
    ],
    'http' => [
        'timeout_seconds' => 40,
        'retry_times' => 2,
        'retry_sleep_ms' => 500,
    ],
    'webhooks' => [
        'resolve_connection_by' => 'connection_id',
        'connection_id_param' => 'connection_id',
        'tenant_id_param' => 'tenant_id',
    ],
    'notifications' => [
        'panel' => env('ESIM_GO_NOTIFY_PANEL', 'tenant'),
        'callback_event' => 'webhook_received',
    ],
    'tables' => [
        'connections' => 'esim_go_connections',
        'catalogue_snapshots' => 'esim_go_catalogue_snapshots',
        'products' => 'esim_go_products',
        'orders' => 'esim_go_orders',
        'esims' => 'esim_go_esims',
        'callbacks' => 'esim_go_callbacks',
        'inventory_usages' => 'esim_go_inventory_usages',
    ],
    'cache' => [
        'store' => env('ESIM_GO_CACHE_STORE'),
    ],
    'logging' => [
        'enabled' => (bool) env('ESIM_GO_LOGGING_ENABLED', true),
    ],
    'fake' => (bool) env('ESIM_GO_FAKE', false),
    'fake_run_id' => env('ESIM_GO_FAKE_RUN_ID'),
    'queue' => env('ESIM_GO_QUEUE', 'providers'),
    'api' => [
        'rate_limit' => env('ESIM_GO_API_RATE_LIMIT', '60,1'),
    ],
    'catalogue' => [
        'cache_seconds' => 3600,
        'per_page' => 100,
    ],
    'fulfillment' => [
        'poll_seconds' => 120,
        'max_attempts' => 6,
    ],
    'inventory' => [
        'refund_enabled' => (bool) env('ESIM_GO_REFUND_ENABLED', false),
        'refund_window_days' => (int) env('ESIM_GO_REFUND_WINDOW_DAYS', 60),
    ],
];
