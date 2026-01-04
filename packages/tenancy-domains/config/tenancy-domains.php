<?php

return [
    'root_domain' => env('PLATFORM_ROOT_DOMAIN'),
    'allowed_hosts' => array_filter(array_map('trim', explode(',', env('PLATFORM_ALLOWED_HOSTS', '')))),
    'verification' => [
        'default_method' => env('PLATFORM_DOMAIN_VERIFICATION_METHOD', 'txt'),
        'cname_target' => env('PLATFORM_CNAME_TARGET'),
    ],
    'tls' => [
        'enabled' => (bool) env('TENANCY_DOMAINS_TLS_ENABLED', false),
        'provider' => env('TENANCY_DOMAINS_TLS_PROVIDER', 'null'),
        'providers' => [
            'null' => \Haida\TenancyDomains\Tls\NullCertificateProvider::class,
        ],
        'mode' => env('TENANCY_DOMAINS_TLS_MODE', 'dns-01'),
        'renew_before_days' => (int) env('TENANCY_DOMAINS_TLS_RENEW_BEFORE_DAYS', 30),
        'retry_minutes' => (int) env('TENANCY_DOMAINS_TLS_RETRY_MINUTES', 30),
        'retry' => [
            'tries' => (int) env('TENANCY_DOMAINS_TLS_RETRY_TRIES', 3),
            'backoff_seconds' => array_filter(array_map('intval', explode(',', env('TENANCY_DOMAINS_TLS_RETRY_BACKOFF', '30,60,120')))),
        ],
        'acme' => [
            'directory_url' => env('TENANCY_DOMAINS_ACME_DIRECTORY'),
            'account_email' => env('TENANCY_DOMAINS_ACME_EMAIL'),
        ],
    ],
    'api' => [
        'rate_limit' => env('TENANCY_DOMAINS_RATE_LIMIT', '60,1'),
        'verify_rate_limit' => env('TENANCY_DOMAINS_VERIFY_RATE_LIMIT', '20,1'),
    ],
    'tables' => [
        'site_domains' => 'site_domains',
    ],
];
