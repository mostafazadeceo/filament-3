<?php

namespace Haida\FilamentMarketplaceConnectors\Support;

class MarketplaceOpenApi
{
    public static function toArray(): array
    {
        return [
            'openapi' => '3.0.0',
            'info' => [
                'title' => 'Marketplace Connectors API',
                'version' => '1.0.0',
            ],
            'paths' => [
                '/api/v1/filament-marketplace-connectors/connectors' => [
                    'get' => ['summary' => 'List connectors'],
                ],
                '/api/v1/filament-marketplace-connectors/connectors/{connector}/sync' => [
                    'post' => ['summary' => 'Trigger sync job'],
                ],
                '/api/v1/filament-marketplace-connectors/openapi' => [
                    'get' => ['summary' => 'OpenAPI spec'],
                ],
            ],
        ];
    }
}
