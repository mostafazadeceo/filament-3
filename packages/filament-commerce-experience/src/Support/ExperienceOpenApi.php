<?php

namespace Haida\FilamentCommerceExperience\Support;

class ExperienceOpenApi
{
    public static function toArray(): array
    {
        return [
            'openapi' => '3.0.0',
            'info' => [
                'title' => 'Commerce Experience API',
                'version' => '1.0.0',
            ],
            'paths' => [
                '/api/v1/filament-commerce-experience/reviews' => [
                    'get' => ['summary' => 'List reviews'],
                ],
                '/api/v1/filament-commerce-experience/questions' => [
                    'get' => ['summary' => 'List questions'],
                ],
                '/api/v1/filament-commerce-experience/csat' => [
                    'post' => ['summary' => 'Create CSAT survey'],
                ],
                '/api/v1/filament-commerce-experience/buy-now' => [
                    'post' => ['summary' => 'Enable buy-now'],
                ],
                '/api/v1/filament-commerce-experience/openapi' => [
                    'get' => ['summary' => 'OpenAPI spec'],
                ],
            ],
        ];
    }
}
