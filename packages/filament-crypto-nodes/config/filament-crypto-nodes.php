<?php

return [
    'tables' => [
        'node_connectors' => 'crypto_node_connectors',
    ],
    'btcpay' => [
        'enabled' => true,
        'base_url' => env('BTCPAY_BASE_URL'),
        'api_key' => env('BTCPAY_API_KEY'),
        'store_id' => env('BTCPAY_STORE_ID'),
        'webhook_secret' => env('BTCPAY_WEBHOOK_SECRET'),
        'timeout' => 10,
    ],

    'bitcoin_core' => [
        'enabled' => false,
        'rpc_url' => env('BITCOIN_RPC_URL'),
        'rpc_user' => env('BITCOIN_RPC_USER'),
        'rpc_password' => env('BITCOIN_RPC_PASSWORD'),
        'timeout' => 10,
    ],

    'evm' => [
        'enabled' => false,
        'rpc_url' => env('EVM_RPC_URL'),
        'timeout' => 10,
    ],
];
