<?php

namespace Haida\PaymentsOrchestrator\Support;

class PaymentsOpenApi
{
    public static function toArray(): array
    {
        return [
            'openapi' => '3.0.0',
            'info' => [
                'title' => 'Commerce Payments API',
                'version' => '1.0.0',
            ],
            'paths' => [
                '/api/v1/commerce-payments/intents' => [
                    'post' => ['summary' => 'Create payment intent'],
                ],
                '/api/v1/commerce-payments/intents/{intent}' => [
                    'get' => ['summary' => 'Show payment intent'],
                ],
                '/api/v1/commerce-payments/webhooks/{provider}' => [
                    'post' => ['summary' => 'Handle payment webhook'],
                ],
                '/api/v1/commerce-payments/openapi' => [
                    'get' => ['summary' => 'OpenAPI spec'],
                ],
            ],
        ];
    }
}
