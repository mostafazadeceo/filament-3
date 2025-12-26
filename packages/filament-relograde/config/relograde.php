<?php

return [
    'base_url' => env('RELOGRADE_BASE_URL', 'https://connect.relograde.com'),
    'api_version' => env('RELOGRADE_API_VERSION', '1.02'),

    'http' => [
        'timeout' => 40,
        'retry_times' => 2,
        'retry_sleep_ms' => 500,
    ],

    'rate_limit' => [
        'max_per_minute' => 60,
    ],

    'cache' => [
        'enabled' => true,
        'store' => null,
        'ttl_seconds' => 3600,
    ],

    'webhooks' => [
        'allowed_ips' => [
            '18.195.134.217',
        ],
        'secret_header' => 'X-Relograde-Secret',
    ],

    'schedule' => [
        'enabled' => true,
        'sync_accounts' => 'hourly',
        'sync_catalog' => 'daily',
        'poll_pending_orders' => 'everyFiveMinutes',
        'check_low_balance_alerts' => 'hourly',
    ],

    'polling' => [
        'older_than_minutes' => 5,
        'limit' => 50,
    ],

    'low_balance_thresholds' => [
        // 'USD' => 50.00,
    ],

    'encrypt_voucher_codes' => true,

    'log_requests' => true,

    'permissions_enabled' => false,

    'permissions' => [
        'view' => 'relograde.view',
        'sync' => 'relograde.sync',
        'orders_create' => 'relograde.orders.create',
        'orders_fulfill' => 'relograde.orders.fulfill',
        'vouchers_reveal' => 'relograde.vouchers.reveal',
        'logs_view' => 'relograde.logs.view',
        'settings_manage' => 'relograde.settings.manage',
    ],
];
