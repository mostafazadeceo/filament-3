<?php

return [
    'tables' => [
        'stores' => 'pos_stores',
        'registers' => 'pos_registers',
        'devices' => 'pos_devices',
        'cashier_sessions' => 'pos_cashier_sessions',
        'cash_movements' => 'pos_cash_movements',
        'sales' => 'pos_sales',
        'sale_items' => 'pos_sale_items',
        'sale_payments' => 'pos_sale_payments',
        'sync_cursors' => 'pos_sync_cursors',
        'outbox' => 'pos_outbox',
    ],
    'defaults' => [
        'currency' => 'IRR',
    ],
    'api' => [
        'rate_limit' => '60,1',
    ],
    'offline' => [
        'max_outbox_batch' => 200,
        'allowed_payment_providers' => ['manual'],
    ],
];
