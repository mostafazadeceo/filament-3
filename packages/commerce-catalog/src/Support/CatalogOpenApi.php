<?php

namespace Haida\CommerceCatalog\Support;

class CatalogOpenApi
{
    public static function toArray(): array
    {
        return [
            'openapi' => '3.0.0',
            'info' => [
                'title' => 'Commerce Catalog API',
                'version' => '1.0.0',
            ],
            'paths' => [
                '/api/v1/commerce-catalog/products' => [
                    'get' => ['summary' => 'List products'],
                    'post' => ['summary' => 'Create product'],
                ],
                '/api/v1/commerce-catalog/products/{product}' => [
                    'get' => ['summary' => 'Show product'],
                    'put' => ['summary' => 'Update product'],
                    'delete' => ['summary' => 'Delete product'],
                ],
                '/api/v1/commerce-catalog/collections' => [
                    'get' => ['summary' => 'List collections'],
                    'post' => ['summary' => 'Create collection'],
                ],
                '/api/v1/commerce-catalog/collections/{collection}' => [
                    'get' => ['summary' => 'Show collection'],
                    'put' => ['summary' => 'Update collection'],
                    'delete' => ['summary' => 'Delete collection'],
                ],
                '/api/v1/commerce-catalog/openapi' => [
                    'get' => ['summary' => 'OpenAPI spec'],
                ],
            ],
        ];
    }
}
