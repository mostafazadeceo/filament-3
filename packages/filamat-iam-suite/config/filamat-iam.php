<?php

use Filamat\IamSuite\Models\Tenant;

return [
    'enable_shield' => false,

    'capability_sync' => [
        'enabled' => true,
        'on_boot' => true,
        'on_http' => true,
        'on_console' => true,
        'queue' => true,
        'guard' => 'web',
        'cooldown_seconds' => 300,
        'lock_seconds' => 30,
        'dirty_ttl_seconds' => 3600,
    ],

    'wallet_engine' => 'internal',

    'notification_adapter' => 'custom_plugin',

    'payment_provider' => 'dummy',

    'tenancy_model' => Tenant::class,

    'shared_data_mode' => 'isolated',

    'shared_models' => [
        Filamat\IamSuite\Models\SubscriptionPlan::class,
        Filamat\IamSuite\Models\PermissionTemplate::class,
    ],

    'features' => [
        'access_requests' => true,
        'permission_snapshots' => true,
        'delegated_admin' => true,
        'audit_hash_chain' => true,
        'wallet_risk_controls' => false,
        'gdpr_tools' => false,
        'user_profiles' => false,
    ],

    'access_requests' => [
        'approval_required' => true,
        'default_access_hours' => 8,
        'request_expires_hours' => 72,
        'apply_roles_as_permissions' => true,
    ],

    'permission_snapshots' => [
        'hash_algo' => 'sha256',
        'max_history' => 50,
    ],

    'delegated_admin' => [
        'enabled' => true,
    ],

    'tables' => [
        'notifications' => 'notifications',
    ],

    'super_admin_panels' => ['admin'],

    'api' => [
        'version' => 'v1',
        'rate_limit' => '60,1',
        'token_name' => 'filamat-iam',
        'api_key_header' => 'X-Api-Key',
        'tenant_header' => 'X-Tenant-ID',
        'enforce_scopes' => true,
    ],

    'webhooks' => [
        'signature_header' => 'X-Filamat-Signature',
        'timestamp_header' => 'X-Filamat-Timestamp',
        'nonce_header' => 'X-Filamat-Nonce',
        'tolerance_seconds' => 300,
        'verify_inbound' => true,
        'replay_protection' => true,
        'nonce_ttl_seconds' => 600,
        'inbound_secrets' => [
            'notification' => env('FILAMAT_IAM_NOTIFICATION_WEBHOOK_SECRET'),
            'payment' => env('FILAMAT_IAM_PAYMENT_WEBHOOK_SECRET'),
        ],
    ],

    'otp' => [
        'length' => 6,
        'expires_minutes' => 5,
        'max_attempts' => 5,
        'lock_minutes' => 15,
        'verify_via_adapter' => true,
        'rate_limit' => [
            'max_attempts' => 10,
            'decay_seconds' => 60,
        ],
    ],

    'wallet' => [
        'allow_multi_currency' => true,
        'default_currency' => 'irr',
        'risk_controls' => [
            'enabled' => false,
            'daily_debit_limit' => null,
            'velocity' => [
                'window_seconds' => 60,
                'max_count' => null,
                'max_amount' => null,
            ],
        ],
    ],

    'subscriptions' => [
        'allow_tenant_plans' => true,
        'enforce_access' => true,
        'active_statuses' => ['active', 'trialing'],
        'exempt_permissions' => ['iam.view', 'iam.manage', 'subscription.view', 'subscription.manage'],
    ],

    'impersonation' => [
        'enabled' => true,
        'max_minutes' => 120,
    ],

    'audit' => [
        'enabled' => true,
        'hash_chain' => true,
        'hash_algo' => 'sha256',
    ],

    'gdpr' => [
        'enabled' => false,
        'retention_days' => 365,
    ],

    'user_profiles' => [
        'enabled' => false,
    ],
];
