<?php

return [
    'tables' => [
        'pages' => 'store_pages',
        'page_versions' => 'store_page_versions',
        'blocks' => 'store_blocks',
        'menus' => 'store_menus',
        'menu_items' => 'store_menu_items',
        'themes' => 'store_themes',
        'redirects' => 'store_redirects',
    ],
    'api' => [
        'rate_limit' => '60,1',
    ],
    'public' => [
        'prefix' => 'storefront',
    ],
];
