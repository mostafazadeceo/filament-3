<?php

use Filamat\IamSuite\Models\Tenant;

return [
    'enable_shield' => false,

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
        'pam' => true,
        'sessions' => true,
        'protected_actions' => true,
        'mfa' => true,
        'sso' => (bool) env('FILAMAT_IAM_FEATURE_SSO', false),
        'scim' => (bool) env('FILAMAT_IAM_FEATURE_SCIM', false),
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
        'notifications' => 'iam_notifications',
    ],

    'super_admin_panels' => ['admin'],
    'mega_super_admins' => [
        'emails' => array_filter(array_map('trim', explode(',', env('FILAMAT_IAM_MEGA_SUPER_ADMINS', '')))),
        'user_ids' => array_filter(array_map('intval', array_filter(explode(',', env('FILAMAT_IAM_MEGA_SUPER_ADMIN_IDS', ''))))),
    ],
    'organization_entitlements' => [
        'exempt_permissions' => [
            'iam.view',
            'subscription.view',
            'subscription.manage',
            'user.view',
        ],
    ],
    'role_templates' => [
        'include_module_permissions' => [
            'tenant_owner' => true,
            'tenant_admin' => false,
        ],
    ],
    'chat' => [
        'shared_base_url' => env('FILAMAT_IAM_CHAT_BASE_URL', 'https://chat.abrak.org'),
        'shared_connection_name' => env('FILAMAT_IAM_CHAT_CONNECTION_NAME', 'shared-rocket-chat'),
        'shared_admin_user_id' => env('FILAMAT_IAM_CHAT_ADMIN_USER_ID', ''),
        'shared_admin_token' => env('FILAMAT_IAM_CHAT_ADMIN_TOKEN', ''),
        'default_team_prefix' => env('ROCKET_CHAT_TEAM_PREFIX', 'tenant-'),
        'default_room_prefix' => env('ROCKET_CHAT_ROOM_PREFIX', 'room-'),
        'owner_manage_flag' => env('FILAMAT_IAM_CHAT_OWNER_MANAGE_FLAG', 'tenant_owner_manage'),
        // When the Rocket.Chat instance is shared across tenants, we still want to prevent
        // Hub users without an active chat entitlement from logging into chat via SSO.
        'oidc_client_id' => env('FILAMAT_IAM_RC_OIDC_CLIENT_ID', 'rocketchat-wordpress'),
        'enforce_oidc_access' => (bool) env('FILAMAT_IAM_RC_OIDC_ENFORCE_CHAT_ACCESS', false),
    ],
    'modules' => [
        'labels' => [
            'filamat-iam-suite' => 'مدیریت هویت و دسترسی',
            'filament-ai-core' => 'هوش مصنوعی',
            'filament-meetings' => 'جلسات',
            'filament-workhub' => 'رهگیری کارها',
            'chat' => 'چت سازمانی',
            'blog' => 'وبلاگ',
            'content-cms' => 'مدیریت محتوا',
            'site-builder-core' => 'سایت‌ساز',
            'page-builder' => 'صفحه‌ساز',
            'filament-storefront-builder' => 'فروشگاه‌ساز',
            'tenancy-domains' => 'دامنه‌ها',
            'platform-core' => 'هسته پلتفرم',
            'feature-gates' => 'قفل قابلیت‌ها',
            'commerce-catalog' => 'کاتالوگ',
            'commerce-checkout' => 'پرداخت و تسویه',
            'commerce-orders' => 'سفارش‌ها',
            'filament-commerce-core' => 'تجارت',
            'filament-commerce-experience' => 'تجربه خرید',
            'filament-pos' => 'صندوق فروش',
            'filament-loyalty-club' => 'باشگاه مشتریان',
            'filament-crypto-core' => 'هسته رمزارز',
            'filament-crypto-gateway' => 'درگاه رمزارز',
            'filament-crypto-nodes' => 'نودهای رمزارز',
            'filament-accounting-ir' => 'حسابداری ایران',
            'filament-payroll-attendance' => 'حضور و غیاب',
            'filament-payroll-attendance-ir' => 'حقوق و دستمزد',
            'filament-petty-cash-ir' => 'تنخواه',
            'filament-restaurant-ops' => 'عملیات رستوران',
            'filament-marketplace-connectors' => 'اتصال مارکت‌پلیس',
            'filament-payments' => 'پرداخت‌ها',
            'payments-orchestrator' => 'هماهنگ‌ساز پرداخت',
            'providers-core' => 'اتصال ارائه‌دهندگان',
            'providers-esim-go' => 'eSIM Go',
            'mailtrap' => 'میل‌ترپ',
            'mailops' => 'عملیات ایمیل',
            'filament-app-api' => 'اپلیکیشن',
            'threecx' => 'مرکز تماس 3CX',
        ],
    ],

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
    'automation' => [
        'enabled' => true,
        'webhook_type' => 'automation',
        'default_auth_mode' => 'hmac+nonce',
        'event_catalog' => 'n8n_event_catalog',
        'rate_limit' => [
            'max_attempts' => 60,
            'decay_seconds' => 60,
        ],
        'retention_days' => [
            'deliveries' => 30,
            'reports' => 90,
        ],
        'redaction_defaults' => [
            'actor' => [
                'email' => 'mask',
                'ip' => 'remove',
                'ua' => 'remove',
            ],
        ],
        'action_proposals' => [
            'enabled' => true,
        ],
        'audit' => [
            'window_days' => 7,
        ],
        'schedule' => [
            'enabled' => true,
            'audit_time' => '02:00',
            'prune_time' => '03:00',
        ],
        'inbound' => [
            'auth_mode' => 'header',
            'token_header' => 'X-N8N-Token',
            'token' => env('FILAMAT_IAM_N8N_INBOUND_TOKEN', ''),
        ],
        'n8n_api' => [
            'enabled' => false,
            'base_url' => env('FILAMAT_IAM_N8N_API_BASE_URL', ''),
            'api_key' => env('FILAMAT_IAM_N8N_API_KEY', ''),
            'health_endpoint' => '/healthz',
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
        'exempt_permissions' => [
            'iam.view',
            'iam.manage',
            'subscription.view',
            'subscription.manage',
            'chat.connection.view',
            'chat.connection.manage',
            'chat.user.view',
            'chat.user.manage',
            'chat.sync',
        ],
    ],

    'governance' => [
        'require_reason' => true,
        'reason_header' => 'X-Change-Reason',
    ],

    'impersonation' => [
        'enabled' => true,
        'max_minutes' => 120,
        'require_reason' => true,
        'require_ticket' => true,
        'restricted_default' => true,
    ],

    'pam' => [
        'enabled' => true,
        'approval_required' => true,
        'max_minutes' => 240,
        'auto_expire_on_boot' => true,
        'auto_expire_cooldown_seconds' => 300,
        'require_mfa_roles' => [],
        'digest' => [
            'enabled' => true,
            'days_ahead' => 7,
            'notify_roles' => ['owner', 'admin'],
        ],
    ],

    'sessions' => [
        'record' => true,
        'retention_days' => 30,
    ],

    'protected_actions' => [
        'enabled' => true,
        'ttl_minutes' => 10,
        'require_mfa_actions' => [
            'iam.impersonate',
            'iam.pam.activate',
            'iam.mfa.reset',
        ],
    ],

    'mfa' => [
        'totp' => [
            'enabled' => true,
            'issuer' => env('APP_NAME', 'Haida Hub'),
            'digits' => 6,
            'period' => 30,
        ],
        'backup_codes' => [
            'count' => 8,
        ],
        'webauthn' => [
            'enabled' => false,
        ],
    ],

    'sso' => [
        'enabled' => (bool) env('FILAMAT_IAM_SSO_ENABLED', false),
        'providers' => [
            'oidc' => true,
            'saml' => false,
        ],
        'oidc' => [
            'issuer' => env('FILAMAT_IAM_OIDC_ISSUER', env('APP_URL', '')),
            'login_url' => env('FILAMAT_IAM_OIDC_LOGIN_URL', '/admin/login'),
            'authorize_path' => env('FILAMAT_IAM_OIDC_AUTHORIZE_PATH', '/oidc/authorize'),
            'token_path' => env('FILAMAT_IAM_OIDC_TOKEN_PATH', '/oidc/token'),
            'userinfo_path' => env('FILAMAT_IAM_OIDC_USERINFO_PATH', '/oidc/userinfo'),
            'jwks_path' => env('FILAMAT_IAM_OIDC_JWKS_PATH', '/oidc/jwks.json'),
            'code_ttl_seconds' => (int) env('FILAMAT_IAM_OIDC_CODE_TTL', 300),
            'token_ttl_seconds' => (int) env('FILAMAT_IAM_OIDC_TOKEN_TTL', 3600),
            'refresh_ttl_seconds' => (int) env('FILAMAT_IAM_OIDC_REFRESH_TTL', 2592000),
            'allowed_scopes' => ['openid', 'profile', 'email', 'roles'],
            'key_path' => env('FILAMAT_IAM_OIDC_KEY_PATH', storage_path('app/oidc')),
            // Static OIDC clients (e.g., Rocket.Chat OAuth client). Keep secrets in env, not in git.
            'clients' => (static function (): array {
                $clientId = trim((string) env('FILAMAT_IAM_RC_OIDC_CLIENT_ID', ''));
                $clientSecret = (string) env('FILAMAT_IAM_RC_OIDC_CLIENT_SECRET', '');

                if ($clientId === '' || $clientSecret === '') {
                    return [];
                }

                $redirectUris = array_values(array_filter(array_map(
                    'trim',
                    explode(',', (string) env('FILAMAT_IAM_RC_OIDC_REDIRECT_URIS', '')),
                )));

                $scopes = preg_split('/\s+/', trim((string) env('FILAMAT_IAM_RC_OIDC_SCOPES', 'openid profile email roles'))) ?: [];
                $scopes = array_values(array_filter(array_map('strval', $scopes)));

                return [
                    $clientId => [
                        'name' => 'Abrak Chat',
                        'client_secret' => $clientSecret,
                        'redirect_uris' => $redirectUris,
                        'scopes' => $scopes,
                    ],
                ];
            })(),
        ],
    ],

    'scim' => [
        'enabled' => false,
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
