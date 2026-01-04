<?php

return [
    'themes' => [
        'relograde-v1' => [
            'name' => 'Relograde v1',
            'version' => '1.0.0',
            'description' => 'Relograde-inspired marketing theme with RTL support.',
            'created_at_jalali' => '1404/10/09',
            'view' => 'theme-engine::themes.relograde-v1.landing',
            'assets' => [
                'css' => 'vendor/theme-engine/relograde-v1.css',
            ],
            'tokens' => [
                'primary' => '#E84140',
                'background' => '#F9F9FB',
                'background_dark' => '#0D0D0D',
                'accent' => '#EC4899',
                'accent_secondary' => '#8B5CF6',
                'text' => '#0F172A',
                'muted' => '#64748B',
            ],
        ],
    ],
];
