<?php

declare(strict_types=1);

return [
    'tables' => [
        'devices' => 'app_devices',
        'device_tokens' => 'app_device_tokens',
        'support_tickets' => 'app_support_tickets',
        'support_messages' => 'app_support_messages',
        'sync_changes' => 'app_sync_changes',
        'refresh_tokens' => 'app_refresh_tokens',
        'tasks' => 'app_tasks',
        'attendance_records' => 'app_attendance_records',
        'signaling_messages' => 'app_signaling_messages',
    ],
    'api' => [
        'rate_limit' => env('FILAMENT_APP_API_RATE_LIMIT', '60,1'),
    ],
    'auth' => [
        'refresh_ttl_minutes' => (int) env('FILAMENT_APP_API_REFRESH_TTL', 60 * 24 * 14),
    ],
    'sync' => [
        'pull_limit' => (int) env('FILAMENT_APP_API_SYNC_PULL_LIMIT', 200),
    ],
    'app_config' => [
        'websocket_url' => env('APP_REALTIME_WS_URL', ''),
        'realtime_fallback' => 'polling',
        'turn_servers' => env('APP_TURN_SERVERS', ''),
        'features' => [
            'webrtc' => true,
            'push' => true,
            'offline' => true,
        ],
        'endpoints' => [
            'pos_openapi' => '/api/v1/filament-pos/openapi',
            'workhub_openapi' => '/api/v1/workhub/openapi',
            'meetings_openapi' => '/api/v1/meetings/openapi',
            'attendance_openapi' => '/api/v1/payroll-attendance/openapi',
            'loyalty_openapi' => '/api/v1/loyalty/openapi',
            'crypto_openapi' => '/api/v1/crypto/openapi',
            'signaling' => '/api/v1/app/realtime/signals',
        ],
    ],
];
