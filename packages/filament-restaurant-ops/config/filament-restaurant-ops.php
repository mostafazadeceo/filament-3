<?php

return [
    'table_prefix' => 'restaurant_',

    'api' => [
        'enabled' => true,
        'rate_limit' => '60,1',
    ],

    'inventory' => [
        'allow_negative' => false,
        'valuation_method' => 'weighted_average',
    ],

    'cost_control' => [
        'default_waste_percent' => 0,
    ],
];
