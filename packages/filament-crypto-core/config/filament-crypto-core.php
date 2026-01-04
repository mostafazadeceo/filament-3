<?php

return [
    'tables' => [
        'accounts' => 'crypto_accounts',
        'ledgers' => 'crypto_ledgers',
        'ledger_entries' => 'crypto_ledger_entries',
        'wallets' => 'crypto_wallets',
        'addresses' => 'crypto_addresses',
        'rates' => 'crypto_rates',
        'network_fees' => 'crypto_network_fees',
        'audit_events' => 'crypto_audit_events',
        'fee_policies' => 'crypto_fee_policies',
    ],
    'defaults' => [
        'currency' => 'USDT',
        'network' => 'TRON',
        'rate_ttl_seconds' => 300,
        'fee_ttl_seconds' => 300,
    ],
    'ledger' => [
        'precision' => 8,
        'default_accounts' => [
            'clearing' => [
                'code' => 'CRYPTO_CLEARING',
                'name_fa' => 'کلیرینگ رمزارز',
                'type' => 'asset',
            ],
            'wallet' => [
                'code' => 'CRYPTO_WALLET',
                'name_fa' => 'کیف‌پول رمزارز',
                'type' => 'asset',
            ],
            'merchant_payable' => [
                'code' => 'MERCHANT_PAYABLE',
                'name_fa' => 'بدهی پذیرنده',
                'type' => 'liability',
            ],
            'platform_revenue' => [
                'code' => 'PLATFORM_REVENUE',
                'name_fa' => 'درآمد پلتفرم',
                'type' => 'revenue',
            ],
            'fee_expense' => [
                'code' => 'FEE_EXPENSE',
                'name_fa' => 'هزینه کارمزد شبکه',
                'type' => 'expense',
            ],
        ],
    ],
    'plans' => [
        'free' => [
            'label' => 'Free',
            'fees' => [
                'invoice_percent' => 0.8,
                'invoice_fixed' => 0,
                'payout_fixed' => 1.0,
                'conversion_percent' => 0,
                'network_fee_mode' => 'pass_through',
            ],
            'features' => [
                'crypto.providers' => true,
                'crypto.payouts' => false,
                'crypto.webhook_replay' => false,
                'crypto.ai_auditor' => false,
                'crypto.nodes' => false,
            ],
        ],
        'pro' => [
            'label' => 'Pro',
            'fees' => [
                'invoice_percent' => 0.6,
                'invoice_fixed' => 0,
                'payout_fixed' => 0.5,
                'conversion_percent' => 0,
                'network_fee_mode' => 'pass_through',
            ],
            'features' => [
                'crypto.providers' => true,
                'crypto.payouts' => true,
                'crypto.webhook_replay' => true,
                'crypto.ai_auditor' => true,
                'crypto.nodes' => false,
            ],
        ],
        'enterprise' => [
            'label' => 'Enterprise',
            'fees' => [
                'invoice_percent' => 0.3,
                'invoice_fixed' => 0,
                'payout_fixed' => 0,
                'conversion_percent' => 0,
                'network_fee_mode' => 'absorb',
            ],
            'features' => [
                'crypto.providers' => true,
                'crypto.payouts' => true,
                'crypto.webhook_replay' => true,
                'crypto.ai_auditor' => true,
                'crypto.nodes' => true,
            ],
        ],
    ],
];
