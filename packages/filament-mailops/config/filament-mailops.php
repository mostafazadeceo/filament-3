<?php

return [
    'tables' => [
        'domains' => 'mailops_domains',
        'mailboxes' => 'mailops_mailboxes',
        'aliases' => 'mailops_aliases',
        'outbound_messages' => 'mailops_outbound_messages',
        'inbound_messages' => 'mailops_inbound_messages',
    ],
    'mailu' => [
        'enabled' => env('MAILOPS_MAILU_ENABLED', false),
        'base_url' => env('MAILOPS_MAILU_BASE_URL', 'https://mail.example.com/api/v1'),
        'token' => env('MAILOPS_MAILU_TOKEN'),
        'verify_tls' => env('MAILOPS_MAILU_VERIFY_TLS', true),
        'timeout' => env('MAILOPS_MAILU_TIMEOUT', 15),
    ],
    'smtp' => [
        'host' => env('MAILOPS_SMTP_HOST', 'mail.abrak.org'),
        'port' => env('MAILOPS_SMTP_PORT', 587),
        'encryption' => env('MAILOPS_SMTP_ENCRYPTION', 'tls'),
    ],
    'imap' => [
        'host' => env('MAILOPS_IMAP_HOST', 'mail.abrak.org'),
        'port' => env('MAILOPS_IMAP_PORT', 993),
        'encryption' => env('MAILOPS_IMAP_ENCRYPTION', 'ssl'),
        'verify_tls' => env('MAILOPS_IMAP_VERIFY_TLS', true),
    ],
    'from' => [
        'address' => env('MAILOPS_DEFAULT_FROM_ADDRESS'),
        'name' => env('MAILOPS_DEFAULT_FROM_NAME'),
    ],
    'api' => [
        'rate_limit' => env('MAILOPS_API_RATE_LIMIT', '60,1'),
    ],
    'inbound' => [
        'sync_limit' => env('MAILOPS_INBOUND_SYNC_LIMIT', 50),
        'store_body' => env('MAILOPS_INBOUND_STORE_BODY', true),
    ],
    'outbound' => [
        'store_body' => env('MAILOPS_OUTBOUND_STORE_BODY', true),
    ],
];
