<?php

namespace Haida\CommerceOrders\Support;

class OrdersOpenApi
{
    public static function toArray(): array
    {
        return [
            'openapi' => '3.0.0',
            'info' => [
                'title' => 'Commerce Orders API',
                'version' => '1.0.0',
            ],
            'paths' => [
                '/api/v1/commerce-orders/orders' => [
                    'get' => ['summary' => 'List orders'],
                ],
                '/api/v1/commerce-orders/orders/{order}' => [
                    'get' => ['summary' => 'Show order'],
                    'patch' => ['summary' => 'Update order'],
                ],
                '/api/v1/commerce-orders/openapi' => [
                    'get' => ['summary' => 'OpenAPI spec'],
                ],
            ],
        ];
    }
}
