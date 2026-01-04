<?php

return [
    'tables' => [
        'provider_accounts' => 'crypto_provider_accounts',
        'invoices' => 'crypto_invoices',
        'invoice_payments' => 'crypto_invoice_payments',
        'payouts' => 'crypto_payouts',
        'payout_destinations' => 'crypto_payout_destinations',
        'webhook_calls' => 'crypto_webhook_calls',
        'reconciliations' => 'crypto_reconciliations',
        'ai_reports' => 'crypto_ai_reports',
    ],
    'defaults' => [
        'currency' => 'USDT',
        'network' => 'TRC20',
        'invoice_lifetime' => 1800,
    ],
    'api' => [
        'rate_limit' => env('CRYPTO_API_RATE_LIMIT', '60,1'),
    ],
    'webhooks' => [
        'max_retries' => 5,
        'retry_delay_seconds' => 60,
        'replay_max_attempts' => 5,
    ],
    'reconcile' => [
        'schedule' => [
            'enabled' => true,
            'invoice_interval_minutes' => 5,
            'daily_at' => '02:00',
        ],
    ],
    'providers' => [
        'cryptomus' => [
            'class' => Haida\FilamentCryptoGateway\Adapters\CryptomusAdapter::class,
            'base_url' => env('CRYPTOMUS_BASE_URL', 'https://api.cryptomus.com/v1'),
            'ip_allowlist' => ['91.227.144.54'],
            'timeout' => 10,
        ],
        'coinbase' => [
            'class' => Haida\FilamentCryptoGateway\Adapters\CoinbaseCommerceAdapter::class,
            'base_url' => env('COINBASE_COMMERCE_BASE_URL', 'https://api.commerce.coinbase.com'),
            'timeout' => 10,
        ],
        'coinpayments' => [
            'class' => Haida\FilamentCryptoGateway\Adapters\CoinPaymentsAdapter::class,
            'base_url' => env('COINPAYMENTS_BASE_URL', 'https://www.coinpayments.net/api.php'),
            'ip_allowlist' => ['104.20.60.246', '104.20.60.247'],
            'timeout' => 10,
        ],
        'btcpay' => [
            'class' => Haida\FilamentCryptoNodes\Adapters\BtcpayServerAdapter::class,
            'timeout' => 10,
        ],
    ],
    'notifications' => [
        'panel' => env('CRYPTO_NOTIFY_PANEL', 'tenant'),
        'audit_event' => env('CRYPTO_NOTIFY_AUDIT_EVENT', 'crypto.audit.report'),
        'invoice_paid_event' => env('CRYPTO_NOTIFY_INVOICE_PAID_EVENT', 'crypto.invoice.paid'),
    ],
    'fees' => [
        'invoice_percent' => 0.5,
        'invoice_fixed' => 0,
        'payout_percent' => 0,
        'payout_fixed' => 0,
        'conversion_percent' => 0,
        'network_fee_mode' => 'pass_through',
    ],
    'payouts' => [
        'require_approval' => true,
        'whitelist' => [
            'enabled' => true,
        ],
    ],
    'plans' => [
        'default' => [
            'providers' => ['cryptomus', 'coinbase', 'coinpayments', 'btcpay'],
            'features' => [
                'payouts' => true,
                'webhook_replay' => true,
                'ai_auditor' => false,
                'nodes' => false,
            ],
        ],
    ],
    'ai' => [
        'enabled' => false,
        'provider' => 'rule_based',
        'store_raw_prompts' => false,
        'webhook_url' => env('CRYPTO_AI_WEBHOOK_URL'),
        'secret' => env('CRYPTO_AI_SECRET'),
        'timeout' => 10,
        'n8n' => [
            'url' => env('CRYPTO_AI_N8N_URL'),
            'secret' => env('CRYPTO_AI_N8N_SECRET'),
            'timeout' => 15,
        ],
    ],
    'fake' => env('CRYPTO_GATEWAY_FAKE', false),
];
