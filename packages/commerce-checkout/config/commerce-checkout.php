<?php

return [
    'tables' => [
        'carts' => 'commerce_checkout_carts',
        'cart_items' => 'commerce_checkout_cart_items',
    ],
    'api' => [
        'rate_limit' => env('COMMERCE_CHECKOUT_API_RATE_LIMIT', '60,1'),
    ],
    'cart' => [
        'expires_after_days' => 7,
    ],
    'inventory' => [
        'enabled' => (bool) env('COMMERCE_CHECKOUT_INVENTORY_ENABLED', true),
        'default_warehouse_id' => env('COMMERCE_CHECKOUT_DEFAULT_WAREHOUSE_ID'),
    ],
];
