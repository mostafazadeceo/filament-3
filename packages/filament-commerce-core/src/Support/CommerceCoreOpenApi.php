<?php

namespace Haida\FilamentCommerceCore\Support;

class CommerceCoreOpenApi
{
    public static function toArray(): array
    {
        return [
            'openapi' => '3.0.0',
            'info' => [
                'title' => 'Commerce Core API',
                'version' => '1.0.0',
            ],
            'paths' => [
                '/api/v1/filament-commerce-core/snapshots/catalog' => [
                    'get' => [
                        'summary' => 'Catalog snapshot',
                        'parameters' => [
                            [
                                'name' => 'since',
                                'in' => 'query',
                                'required' => false,
                                'schema' => [
                                    'type' => 'string',
                                    'format' => 'date-time',
                                ],
                            ],
                        ],
                    ],
                ],
                '/api/v1/filament-commerce-core/snapshots/pricing' => [
                    'get' => [
                        'summary' => 'Pricing snapshot',
                        'parameters' => [
                            [
                                'name' => 'since',
                                'in' => 'query',
                                'required' => false,
                                'schema' => [
                                    'type' => 'string',
                                    'format' => 'date-time',
                                ],
                            ],
                        ],
                    ],
                ],
                '/api/v1/filament-commerce-core/snapshots/inventory' => [
                    'get' => [
                        'summary' => 'Inventory snapshot',
                        'parameters' => [
                            [
                                'name' => 'since',
                                'in' => 'query',
                                'required' => false,
                                'schema' => [
                                    'type' => 'string',
                                    'format' => 'date-time',
                                ],
                            ],
                        ],
                    ],
                ],
                '/api/v1/filament-commerce-core/openapi' => [
                    'get' => [
                        'summary' => 'OpenAPI spec',
                    ],
                ],
            ],
        ];
    }
}
