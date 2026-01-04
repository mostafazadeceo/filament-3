<?php

return [
    'table_prefix' => 'petty_cash_',

    'api' => [
        'enabled' => true,
        'rate_limit' => '60,1',
    ],

    'workflow' => [
        'require_attachments' => true,
        'auto_submit_on_create' => false,
        'allow_edit_after_submit' => false,
    ],

    'attachments' => [
        'disk' => 'public',
        'duplicate_detection' => true,
    ],

    'alerts' => [
        'threshold_enabled' => true,
    ],

    'idempotency' => [
        'enabled' => true,
    ],

    'ai' => [
        'enabled' => false,
        'provider' => \Haida\FilamentPettyCashIr\Infrastructure\Ai\FakeAiProvider::class,
        'allow_store_prompts' => false,
        'redaction' => ['description', 'reference', 'payee_name'],
        'log_retention_days' => 30,
        'anomaly_threshold' => 0.7,
        'create_exceptions' => true,
        'max_scan' => 200,
    ],
];
