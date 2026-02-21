<?php

return [
    'tables' => [
        'connections' => 'chat_connections',
        'user_links' => 'chat_user_links',
    ],
    'providers' => [
        'rocket_chat' => [
            'label' => 'Rocket.Chat',
            'class' => \Haida\FilamentChat\Services\Providers\RocketChatProvider::class,
            'timeout' => env('ROCKET_CHAT_TIMEOUT', 10),
            'verify_tls' => env('ROCKET_CHAT_VERIFY_TLS', true),
            'default_roles' => ['user'],
            'role_map' => [
                'tenant_owner' => ['user'],
                'tenant_admin' => ['user'],
                'tenant_member' => ['user'],
            ],
            'team_prefix' => env('ROCKET_CHAT_TEAM_PREFIX', 'tenant-'),
            'room_prefix' => env('ROCKET_CHAT_ROOM_PREFIX', 'room-'),
            'ignore_2fa' => env('ROCKET_CHAT_IGNORE_2FA', true),
            'sync_profile' => env('ROCKET_CHAT_SYNC_PROFILE', true),
            'sync_roles' => env('ROCKET_CHAT_SYNC_ROLES', false),
        ],
    ],
    'default_provider' => 'rocket_chat',
    'api' => [
        'rate_limit' => env('FILAMENT_CHAT_API_RATE_LIMIT', '60,1'),
    ],
    'auto_sync' => env('FILAMENT_CHAT_AUTO_SYNC', false),
    'auto_deactivate' => env('FILAMENT_CHAT_AUTO_DEACTIVATE', false),
    'navigation' => [
        'group' => 'یکپارچه سازی ها',
        'sort' => 45,
    ],
    'fake' => env('FILAMENT_CHAT_FAKE', false),
];
