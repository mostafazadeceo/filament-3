<?php

namespace Haida\FilamentRestaurantOps\Support;

class RestaurantOpsOpenApi
{
    public static function toArray(): array
    {
        return [
            'openapi' => '3.0.0',
            'info' => [
                'title' => 'Restaurant Ops API',
                'version' => '1.0.0',
            ],
            'paths' => [
                '/api/v1/restaurant-ops/suppliers' => [
                    'get' => ['summary' => 'List suppliers'],
                    'post' => ['summary' => 'Create supplier'],
                ],
                '/api/v1/restaurant-ops/suppliers/{supplier}' => [
                    'get' => ['summary' => 'Show supplier'],
                    'put' => ['summary' => 'Update supplier'],
                    'delete' => ['summary' => 'Delete supplier'],
                ],
                '/api/v1/restaurant-ops/uoms' => [
                    'get' => ['summary' => 'List UOMs'],
                    'post' => ['summary' => 'Create UOM'],
                ],
                '/api/v1/restaurant-ops/uoms/{uom}' => [
                    'get' => ['summary' => 'Show UOM'],
                    'put' => ['summary' => 'Update UOM'],
                    'delete' => ['summary' => 'Delete UOM'],
                ],
                '/api/v1/restaurant-ops/items' => [
                    'get' => ['summary' => 'List items'],
                    'post' => ['summary' => 'Create item'],
                ],
                '/api/v1/restaurant-ops/items/{item}' => [
                    'get' => ['summary' => 'Show item'],
                    'put' => ['summary' => 'Update item'],
                    'delete' => ['summary' => 'Delete item'],
                ],
                '/api/v1/restaurant-ops/warehouses' => [
                    'get' => ['summary' => 'List warehouses'],
                    'post' => ['summary' => 'Create warehouse'],
                ],
                '/api/v1/restaurant-ops/warehouses/{warehouse}' => [
                    'get' => ['summary' => 'Show warehouse'],
                    'put' => ['summary' => 'Update warehouse'],
                    'delete' => ['summary' => 'Delete warehouse'],
                ],
                '/api/v1/restaurant-ops/purchase-requests' => [
                    'get' => ['summary' => 'List purchase requests'],
                    'post' => ['summary' => 'Create purchase request'],
                ],
                '/api/v1/restaurant-ops/purchase-requests/{purchase_request}' => [
                    'get' => ['summary' => 'Show purchase request'],
                    'put' => ['summary' => 'Update purchase request'],
                    'delete' => ['summary' => 'Delete purchase request'],
                ],
                '/api/v1/restaurant-ops/purchase-orders' => [
                    'get' => ['summary' => 'List purchase orders'],
                    'post' => ['summary' => 'Create purchase order'],
                ],
                '/api/v1/restaurant-ops/purchase-orders/{purchase_order}' => [
                    'get' => ['summary' => 'Show purchase order'],
                    'put' => ['summary' => 'Update purchase order'],
                    'delete' => ['summary' => 'Delete purchase order'],
                ],
                '/api/v1/restaurant-ops/goods-receipts' => [
                    'get' => ['summary' => 'List goods receipts'],
                    'post' => ['summary' => 'Create goods receipt'],
                ],
                '/api/v1/restaurant-ops/goods-receipts/{goods_receipt}' => [
                    'get' => ['summary' => 'Show goods receipt'],
                    'put' => ['summary' => 'Update goods receipt'],
                    'delete' => ['summary' => 'Delete goods receipt'],
                ],
                '/api/v1/restaurant-ops/goods-receipts/{goods_receipt}/post' => [
                    'post' => ['summary' => 'Post goods receipt'],
                ],
                '/api/v1/restaurant-ops/inventory-docs' => [
                    'get' => ['summary' => 'List inventory docs'],
                    'post' => ['summary' => 'Create inventory doc'],
                ],
                '/api/v1/restaurant-ops/inventory-docs/{inventory_doc}' => [
                    'get' => ['summary' => 'Show inventory doc'],
                    'put' => ['summary' => 'Update inventory doc'],
                    'delete' => ['summary' => 'Delete inventory doc'],
                ],
                '/api/v1/restaurant-ops/inventory-docs/{inventory_doc}/post' => [
                    'post' => ['summary' => 'Post inventory doc'],
                ],
                '/api/v1/restaurant-ops/recipes' => [
                    'get' => ['summary' => 'List recipes'],
                    'post' => ['summary' => 'Create recipe'],
                ],
                '/api/v1/restaurant-ops/recipes/{recipe}' => [
                    'get' => ['summary' => 'Show recipe'],
                    'put' => ['summary' => 'Update recipe'],
                    'delete' => ['summary' => 'Delete recipe'],
                ],
                '/api/v1/restaurant-ops/menu-items' => [
                    'get' => ['summary' => 'List menu items'],
                    'post' => ['summary' => 'Create menu item'],
                ],
                '/api/v1/restaurant-ops/menu-items/{menu_item}' => [
                    'get' => ['summary' => 'Show menu item'],
                    'put' => ['summary' => 'Update menu item'],
                    'delete' => ['summary' => 'Delete menu item'],
                ],
                '/api/v1/restaurant-ops/menu-sales' => [
                    'get' => ['summary' => 'List menu sales'],
                    'post' => ['summary' => 'Create menu sale'],
                ],
                '/api/v1/restaurant-ops/menu-sales/{menu_sale}' => [
                    'get' => ['summary' => 'Show menu sale'],
                    'put' => ['summary' => 'Update menu sale'],
                    'delete' => ['summary' => 'Delete menu sale'],
                ],
                '/api/v1/restaurant-ops/menu-sales/{menu_sale}/post' => [
                    'post' => ['summary' => 'Post menu sale'],
                ],
                '/api/v1/restaurant-ops/openapi' => [
                    'get' => ['summary' => 'OpenAPI spec'],
                ],
            ],
        ];
    }
}
