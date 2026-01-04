<?php

return [
    'enabled' => env('AI_CORE_ENABLED', false),

    'default_provider' => env('AI_CORE_PROVIDER', 'mock'),

    'retention_days' => 30,
    'consent_required_meetings' => true,
    'allow_store_transcripts' => false,

    'rate_limit' => [
        'enabled' => true,
        'max_per_minute' => 60,
        'overrides' => [
            // 'meetings.minutes' => 15,
        ],
    ],

    'circuit_breaker' => [
        'enabled' => true,
        'failure_threshold' => 3,
        'window_seconds' => 300,
        'cooldown_seconds' => 600,
        'notify' => true,
    ],

    'notifications' => [
        'panel' => 'tenant',
    ],

    'redaction' => [
        'defaults' => [
            'emails' => 'mask',
            'phones' => 'mask',
            'ip' => 'remove',
            'ua' => 'remove',
            'sensitive_terms' => [],
        ],
    ],

    'providers' => [
        'mock' => [
            'enabled' => true,
            'model' => 'mock-v1',
        ],
        'n8n' => [
            'enabled' => env('AI_N8N_ENABLED', false),
            'base_url' => env('AI_N8N_BASE_URL', ''),
            'secret' => env('AI_N8N_SECRET', ''),
            'timeout' => env('AI_N8N_TIMEOUT', 15),
            'signature_header' => env('AI_N8N_SIGNATURE_HEADER', config('filamat-iam.webhooks.signature_header', 'X-Filamat-Signature')),
            'timestamp_header' => env('AI_N8N_TIMESTAMP_HEADER', config('filamat-iam.webhooks.timestamp_header', 'X-Filamat-Timestamp')),
            'nonce_header' => env('AI_N8N_NONCE_HEADER', config('filamat-iam.webhooks.nonce_header', 'X-Filamat-Nonce')),
            'idempotency_header' => env('AI_N8N_IDEMPOTENCY_HEADER', 'X-Idempotency-Key'),
            'tolerance_seconds' => env('AI_N8N_TOLERANCE_SECONDS', 300),
            'nonce_ttl_seconds' => env('AI_N8N_NONCE_TTL_SECONDS', 600),
        ],
        'openai' => [
            'enabled' => env('AI_OPENAI_ENABLED', false),
            'api_key' => env('OPENAI_API_KEY'),
            'model' => env('OPENAI_MODEL', 'gpt-4o-mini'),
            'timeout' => env('OPENAI_TIMEOUT', 15),
        ],
    ],

    'provider_map' => [
        'mock' => Haida\FilamentAiCore\Providers\MockAiProvider::class,
        'n8n' => Haida\FilamentAiCore\Providers\N8nAiProvider::class,
        'openai' => Haida\FilamentAiCore\Providers\OpenAiProvider::class,
    ],
];
