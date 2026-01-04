<?php

namespace Haida\CommerceCheckout\Support;

class CheckoutOpenApi
{
    public static function toArray(): array
    {
        return [
            'openapi' => '3.0.0',
            'info' => [
                'title' => 'Commerce Checkout API',
                'version' => '1.0.0',
            ],
            'paths' => [
                '/api/v1/commerce-checkout/carts' => [
                    'get' => ['summary' => 'List carts'],
                    'post' => ['summary' => 'Create cart'],
                ],
                '/api/v1/commerce-checkout/carts/{cart}' => [
                    'get' => ['summary' => 'Show cart'],
                ],
                '/api/v1/commerce-checkout/carts/{cart}/items' => [
                    'post' => ['summary' => 'Add cart item'],
                ],
                '/api/v1/commerce-checkout/cart-items/{item}' => [
                    'patch' => ['summary' => 'Update cart item'],
                    'delete' => ['summary' => 'Remove cart item'],
                ],
                '/api/v1/commerce-checkout/checkout' => [
                    'post' => ['summary' => 'Checkout cart'],
                ],
                '/api/v1/commerce-checkout/openapi' => [
                    'get' => ['summary' => 'OpenAPI spec'],
                ],
            ],
        ];
    }
}
