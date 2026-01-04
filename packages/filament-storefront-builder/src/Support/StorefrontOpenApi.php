<?php

namespace Haida\FilamentStorefrontBuilder\Support;

class StorefrontOpenApi
{
    public static function toArray(): array
    {
        return [
            'openapi' => '3.0.0',
            'info' => [
                'title' => 'Storefront Builder API',
                'version' => '1.0.0',
            ],
            'paths' => [
                '/api/v1/filament-storefront-builder/openapi' => [
                    'get' => ['summary' => 'OpenAPI spec'],
                ],
                '/storefront/pages/{slug}' => [
                    'get' => ['summary' => 'Public page by slug'],
                ],
                '/storefront/menus/{key}' => [
                    'get' => ['summary' => 'Public menu by key'],
                ],
                '/storefront/blocks/{key}' => [
                    'get' => ['summary' => 'Public block by key'],
                ],
                '/storefront/theme' => [
                    'get' => ['summary' => 'Active theme'],
                ],
            ],
        ];
    }
}
