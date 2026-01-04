<?php

declare(strict_types=1);

namespace Haida\ProvidersEsimGoCore\Support;

class EsimGoOpenApi
{
    public static function toArray(): array
    {
        return [
            'openapi' => '3.0.0',
            'info' => [
                'title' => 'eSIM Go Provider API',
                'version' => '1.0.0',
            ],
            'paths' => [
                '/api/v1/providers/esim-go/connections' => [
                    'get' => ['summary' => 'List connections'],
                ],
                '/api/v1/providers/esim-go/products' => [
                    'get' => ['summary' => 'List products'],
                ],
                '/api/v1/providers/esim-go/orders' => [
                    'get' => ['summary' => 'List orders'],
                ],
                '/api/v1/providers/esim-go/sync' => [
                    'post' => ['summary' => 'Sync catalogue or inventory'],
                ],
                '/api/v1/providers/esim-go/openapi' => [
                    'get' => ['summary' => 'OpenAPI spec'],
                ],
            ],
        ];
    }
}
