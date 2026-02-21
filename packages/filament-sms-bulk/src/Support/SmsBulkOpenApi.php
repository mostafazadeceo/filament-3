<?php

declare(strict_types=1);

namespace Haida\SmsBulk\Support;

class SmsBulkOpenApi
{
    /** @return array<string, mixed> */
    public static function build(): array
    {
        return [
            'openapi' => '3.0.3',
            'info' => [
                'title' => 'SMS Bulk API',
                'version' => '1.0.0',
                'description' => 'Bulk SMS and reseller operations API.',
            ],
            'paths' => [
                '/api/v1/sms-bulk/credit' => [
                    'get' => ['summary' => 'Get credit snapshot'],
                ],
                '/api/v1/sms-bulk/campaigns' => [
                    'get' => ['summary' => 'List campaigns'],
                    'post' => ['summary' => 'Create campaign draft'],
                ],
                '/api/v1/sms-bulk/campaigns/{id}/submit' => [
                    'post' => ['summary' => 'Submit campaign'],
                ],
                '/api/v1/sms-bulk/optout' => [
                    'post' => ['summary' => 'Register opt-out'],
                ],
                '/api/v1/sms-bulk/openapi' => [
                    'get' => ['summary' => 'OpenAPI spec'],
                ],
            ],
        ];
    }
}
