<?php

return [
    'tables' => [
        'pages' => 'content_cms_pages',
        'page_revisions' => 'content_cms_page_revisions',
    ],
    'public' => [
        'home_slug' => 'home',
        'reserved_slugs' => [
            'api',
            'blog',
            'storefront',
            'chat',
            'storage',
            'admin',
            'auth',
            'login',
            'logout',
            'ttt',
            'filament-notify-sw.js',
        ],
    ],
    'api' => [
        'rate_limit' => '60,1',
    ],
];
