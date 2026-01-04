<?php

namespace Haida\FilamentThreeCx\Support;

class ThreeCxOpenApi
{
    public static function toArray(): array
    {
        return [
            'openapi' => '3.0.3',
            'info' => [
                'title' => '3CX API',
                'version' => '1.0.0',
            ],
            'servers' => [
                ['url' => url('/api/v1/threecx')],
            ],
            'paths' => [
                '/crm/lookup' => [
                    'get' => [
                        'summary' => 'Lookup contact by phone or email',
                        'responses' => [
                            '200' => ['description' => 'OK'],
                        ],
                    ],
                ],
                '/crm/search' => [
                    'get' => [
                        'summary' => 'Search contacts',
                        'responses' => [
                            '200' => ['description' => 'OK'],
                        ],
                    ],
                ],
                '/crm/contacts' => [
                    'post' => [
                        'summary' => 'Create contact',
                        'responses' => [
                            '201' => ['description' => 'Created'],
                        ],
                    ],
                ],
                '/crm/journal/call' => [
                    'post' => [
                        'summary' => 'Journal call',
                        'responses' => [
                            '201' => ['description' => 'Created'],
                        ],
                    ],
                ],
                '/crm/journal/chat' => [
                    'post' => [
                        'summary' => 'Journal chat',
                        'responses' => [
                            '201' => ['description' => 'Created'],
                        ],
                    ],
                ],
                '/openapi' => [
                    'get' => [
                        'summary' => 'OpenAPI spec',
                        'responses' => [
                            '200' => ['description' => 'OK'],
                        ],
                    ],
                ],
            ],
        ];
    }
}
