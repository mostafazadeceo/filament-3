<?php

return [
    'tables' => [
        'gateway_connections' => 'payment_gateway_connections',
        'payment_intents' => 'payment_intents',
        'webhook_events' => 'payment_webhook_events',
    ],
    'adapters' => [
        'dummy' => Haida\PaymentsOrchestrator\Adapters\DummyGatewayAdapter::class,
        'hmac' => Haida\PaymentsOrchestrator\Adapters\HmacGatewayAdapter::class,
    ],
    'webhooks' => [
        'signature_header' => 'X-Signature',
        'timestamp_header' => 'X-Timestamp',
        'tolerance_seconds' => 300,
    ],
    'fake' => (bool) env('PAYMENTS_ORCHESTRATOR_FAKE', false),
    'fake_run_id' => env('PAYMENTS_ORCHESTRATOR_FAKE_RUN_ID'),
    'api' => [
        'rate_limit' => env('PAYMENTS_API_RATE_LIMIT', '60,1'),
    ],
];
