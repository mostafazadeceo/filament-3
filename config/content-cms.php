<?php

return [
    'tables' => [
        'pages' => 'content_cms_pages',
        'page_revisions' => 'content_cms_page_revisions',
    ],
    'public' => [
        'home_slug' => 'home',
        // Reserved routes that should never be captured by the CMS catch-all "{slug}" page route.
        'reserved_slugs' => [
            'api',
            'blog',
            'ttt',
            'login',
            'logout',
            'auth',
            'admin',
            'storefront',
            'chat',
            'storage',
            'filament-notify-sw.js',
        ],
    ],
    'api' => [
        'rate_limit' => '60,1',
    ],
];
