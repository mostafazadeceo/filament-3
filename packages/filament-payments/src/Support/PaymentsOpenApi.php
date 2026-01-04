<?php

namespace Haida\FilamentPayments\Support;

class PaymentsOpenApi
{
    public static function toArray(): array
    {
        return [
            'openapi' => '3.0.0',
            'info' => [
                'title' => 'Filament Payments API',
                'version' => '1.0.0',
            ],
            'paths' => [
                '/api/v1/filament-payments/intents' => [
                    'post' => ['summary' => 'Create payment intent'],
                ],
                '/api/v1/filament-payments/intents/{intent}' => [
                    'get' => ['summary' => 'Show payment intent'],
                ],
                '/api/v1/filament-payments/webhooks/{provider}' => [
                    'post' => ['summary' => 'Handle payment webhook'],
                ],
                '/api/v1/filament-payments/openapi' => [
                    'get' => ['summary' => 'OpenAPI spec'],
                ],
            ],
        ];
    }
}
