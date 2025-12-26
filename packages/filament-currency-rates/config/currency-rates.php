<?php

return [
    'enabled' => true,

    'source' => 'alanchand',

    'sources' => [
        'alanchand' => [
            'url' => 'https://alanchand.com/',
            'timeout' => 30,
            'retry_times' => 2,
            'retry_sleep_ms' => 500,
            'user_agent' => 'Mozilla/5.0 (compatible; HaidaCurrencyRates/1.0; +https://alanchand.com)',
        ],
        'custom_api' => [
            'url' => env('CURRENCY_RATES_API_URL'),
            'token' => env('CURRENCY_RATES_API_TOKEN'),
        ],
    ],

    'currencies' => [
        'usd' => 'دلار آمریکا',
        'eur' => 'یورو',
        'gbp' => 'پوند انگلیس',
        'aed' => 'درهم',
        'cny' => 'یوان چین',
    ],

    'units' => [
        'source' => 'irt',
        'display' => 'irt',
    ],

    'pricing' => [
        'base_rate' => 'sell',
        'profit' => [
            'enabled' => false,
            'percent' => 0,
            'fixed_amount' => 0,
            'fixed_unit' => 'irt',
        ],
    ],

    'schedule' => [
        'enabled' => true,
        'every_minutes' => 30,
    ],

    'api' => [
        'enabled' => true,
        'token' => env('CURRENCY_RATES_PUBLIC_TOKEN'),
        'token_header' => 'X-Rate-Token',
    ],

    'cache' => [
        'enabled' => true,
        'ttl_seconds' => 1200,
    ],
];
