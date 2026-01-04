<?php

return [
    'api' => [
        'rate_limit' => '60,1',
    ],
    'ai' => [
        'queue' => [
            'enabled' => false,
            'connection' => null,
            'queue' => null,
        ],
        'agenda' => [
            'default_timebox_minutes' => 10,
            'max_items' => 12,
        ],
        'minutes' => [
            'default_format' => 'team',
        ],
    ],
    'consent' => [
        'message' => 'در این جلسه از قابلیت‌های هوش مصنوعی برای خلاصه‌سازی و صورتجلسه استفاده می‌شود. با ادامه، رضایت خود را اعلام می‌کنید.',
        'voice_script' => 'این جلسه با کمک هوش مصنوعی ثبت و خلاصه می‌شود. اگر موافق هستید، اعلام کنید.',
    ],
    'transcripts' => [
        'provider_enabled' => false,
        'upload_max_kb' => 10240,
        'default_language' => 'fa',
    ],
    'notifications' => [
        'panel' => 'tenant',
    ],
    'exports' => [
        'minutes_format' => 'markdown',
    ],
];
