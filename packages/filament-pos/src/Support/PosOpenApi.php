<?php

namespace Haida\FilamentPos\Support;

class PosOpenApi
{
    public static function toArray(): array
    {
        return [
            'openapi' => '3.0.0',
            'info' => [
                'title' => 'POS API',
                'version' => '1.0.0',
            ],
            'paths' => [
                '/api/v1/filament-pos/sync/snapshot' => [
                    'get' => ['summary' => 'Initial snapshot sync'],
                ],
                '/api/v1/filament-pos/sync/delta' => [
                    'get' => ['summary' => 'Delta sync'],
                ],
                '/api/v1/filament-pos/sync/outbox' => [
                    'post' => ['summary' => 'Upload offline outbox events'],
                ],
                '/api/v1/filament-pos/sales' => [
                    'post' => ['summary' => 'Create POS sale'],
                ],
                '/api/v1/filament-pos/openapi' => [
                    'get' => ['summary' => 'OpenAPI spec'],
                ],
            ],
        ];
    }
}
