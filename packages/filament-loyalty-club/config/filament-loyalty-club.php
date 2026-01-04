<?php

return [
    'table_prefix' => 'loyalty_',

    'api' => [
        'rate_limit' => env('LOYALTY_API_RATE_LIMIT', '60,1'),
    ],

    'features' => [
        'cashback' => [
            'enabled' => (bool) env('LOYALTY_CASHBACK_ENABLED', false),
            'adapter' => env('LOYALTY_CASHBACK_ADAPTER', 'internal'),
            'currency' => env('LOYALTY_CASHBACK_CURRENCY', 'irr'),
            'fallback_to_internal' => (bool) env('LOYALTY_CASHBACK_FALLBACK_INTERNAL', true),
        ],
        'leaderboards' => (bool) env('LOYALTY_LEADERBOARDS_ENABLED', false),
        'ai' => [
            'enabled' => (bool) env('LOYALTY_AI_ENABLED', false),
            'provider' => env('LOYALTY_AI_PROVIDER', 'fake'),
        ],
        'charity_redemption' => (bool) env('LOYALTY_CHARITY_ENABLED', true),
    ],

    'points' => [
        'expiry' => [
            'strategy' => env('LOYALTY_POINTS_EXPIRY_STRATEGY', 'fixed'),
            'default_days' => (int) env('LOYALTY_POINTS_EXPIRY_DAYS', 365),
            'notify_days_before' => array_map('intval', explode(',', env('LOYALTY_POINTS_EXPIRY_NOTIFY_DAYS', '30,7,1'))),
        ],
        'caps' => [
            'daily' => (int) env('LOYALTY_POINTS_CAP_DAILY', 10000),
            'weekly' => (int) env('LOYALTY_POINTS_CAP_WEEKLY', 50000),
        ],
        'allow_transfer' => (bool) env('LOYALTY_POINTS_TRANSFER', false),
    ],

    'tiers' => [
        'cycle_months' => (int) env('LOYALTY_TIER_CYCLE_MONTHS', 12),
        'reset_strategy' => env('LOYALTY_TIER_RESET_STRATEGY', 'anniversary'),
    ],

    'referrals' => [
        'default_waiting_days' => (int) env('LOYALTY_REFERRAL_WAIT_DAYS', 14),
        'max_per_referrer' => (int) env('LOYALTY_REFERRAL_MAX_PER_REFERRER', 10),
        'period_days' => (int) env('LOYALTY_REFERRAL_PERIOD_DAYS', 30),
        'fraud' => [
            'min_days_since_signup' => (int) env('LOYALTY_REFERRAL_MIN_DAYS_SINCE_SIGNUP', 3),
            'block_self_referral' => (bool) env('LOYALTY_REFERRAL_BLOCK_SELF', true),
            'device_uniqueness' => (bool) env('LOYALTY_REFERRAL_DEVICE_UNIQUENESS', false),
            'email_phone_uniqueness' => (bool) env('LOYALTY_REFERRAL_IDENTITY_UNIQUENESS', true),
        ],
    ],

    'segments' => [
        'rfm' => [
            'recency_days' => [30, 90, 180],
            'frequency_thresholds' => [1, 3, 5],
            'monetary_thresholds' => [1000000, 5000000, 20000000],
        ],
    ],

    'events' => [
        'allowed_sources' => array_filter(explode(',', env('LOYALTY_EVENT_SOURCES', 'orders,invoices,admin,api'))),
    ],

    'campaigns' => [
        'panel' => env('LOYALTY_CAMPAIGN_PANEL', 'tenant'),
        'dispatch_event' => env('LOYALTY_CAMPAIGN_TRIGGER', 'loyalty_campaign_dispatched'),
    ],

    'retention' => [
        'audit_days' => (int) env('LOYALTY_RETENTION_AUDIT_DAYS', 730),
        'events_days' => (int) env('LOYALTY_RETENTION_EVENTS_DAYS', 365),
        'fraud_days' => (int) env('LOYALTY_RETENTION_FRAUD_DAYS', 730),
        'campaign_days' => (int) env('LOYALTY_RETENTION_CAMPAIGN_DAYS', 365),
    ],

    'privacy' => [
        'redact_logs' => (bool) env('LOYALTY_REDACT_LOGS', true),
        'store_ip' => (bool) env('LOYALTY_STORE_IP', false),
    ],
];
