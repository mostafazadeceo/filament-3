<?php

return [
    'tables' => [
        'orders' => 'commerce_orders',
        'order_items' => 'commerce_order_items',
        'order_payments' => 'commerce_order_payments',
        'order_returns' => 'commerce_order_returns',
        'order_return_items' => 'commerce_order_return_items',
        'order_refunds' => 'commerce_order_refunds',
    ],
    'api' => [
        'rate_limit' => env('COMMERCE_ORDERS_API_RATE_LIMIT', '60,1'),
    ],
    'numbers' => [
        'prefix' => 'ORD-',
        'pad' => 8,
    ],
];
