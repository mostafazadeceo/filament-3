<?php

return [
    'tables' => [
        'payment_intents' => 'payments_payment_intents',
        'payment_attempts' => 'payments_payment_attempts',
        'webhook_events' => 'payments_webhook_events',
        'provider_connections' => 'payments_provider_connections',
        'refunds' => 'payments_refunds',
        'reconciliations' => 'payments_reconciliations',
    ],
    'defaults' => [
        'currency' => 'IRR',
    ],
    'api' => [
        'rate_limit' => '60,1',
    ],
    'webhooks' => [
        'signature_header' => 'X-Signature',
        'timestamp_header' => 'X-Timestamp',
        'tolerance_seconds' => 300,
    ],
    'providers' => [
        'manual' => [
            'class' => Haida\FilamentPayments\Providers\ManualExternalTerminalProvider::class,
        ],
        'iran_rest' => [
            'class' => Haida\FilamentPayments\Providers\IranRestGatewayProvider::class,
            'fake' => (bool) env('IRAN_REST_GATEWAY_FAKE', true),
            'endpoint' => env('IRAN_REST_GATEWAY_ENDPOINT'),
            'merchant_id' => env('IRAN_REST_GATEWAY_MERCHANT_ID'),
            'callback_url' => env('IRAN_REST_GATEWAY_CALLBACK_URL'),
        ],
        'iran_soap' => [
            'class' => Haida\FilamentPayments\Providers\IranSoapGatewayProvider::class,
            'wsdl' => env('IRAN_SOAP_GATEWAY_WSDL'),
            'redirect_url' => env('IRAN_SOAP_GATEWAY_REDIRECT_URL'),
        ],
        'intl_redirect' => [
            'class' => Haida\FilamentPayments\Providers\InternationalRedirectProvider::class,
            'redirect_url' => env('INTL_REDIRECT_GATEWAY_REDIRECT_URL'),
        ],
    ],
];
