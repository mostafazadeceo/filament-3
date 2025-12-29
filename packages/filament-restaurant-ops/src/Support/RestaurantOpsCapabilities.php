<?php

namespace Haida\FilamentRestaurantOps\Support;

use Filamat\IamSuite\Contracts\CapabilityRegistryInterface;
use Haida\FilamentRestaurantOps\Policies\RestaurantGoodsReceiptPolicy;
use Haida\FilamentRestaurantOps\Policies\RestaurantInventoryDocPolicy;
use Haida\FilamentRestaurantOps\Policies\RestaurantItemPolicy;
use Haida\FilamentRestaurantOps\Policies\RestaurantMenuItemPolicy;
use Haida\FilamentRestaurantOps\Policies\RestaurantMenuSalePolicy;
use Haida\FilamentRestaurantOps\Policies\RestaurantPurchaseOrderPolicy;
use Haida\FilamentRestaurantOps\Policies\RestaurantPurchaseRequestPolicy;
use Haida\FilamentRestaurantOps\Policies\RestaurantRecipePolicy;
use Haida\FilamentRestaurantOps\Policies\RestaurantSupplierPolicy;
use Haida\FilamentRestaurantOps\Policies\RestaurantUomPolicy;
use Haida\FilamentRestaurantOps\Policies\RestaurantWarehousePolicy;

final class RestaurantOpsCapabilities
{
    private static bool $registered = false;

    public static function register(CapabilityRegistryInterface $registry): void
    {
        if (self::$registered) {
            return;
        }

        $registry->register(
            'filament-restaurant-ops',
            self::permissions(),
            [
                'restaurant_ops' => true,
            ],
            [],
            [
                RestaurantSupplierPolicy::class,
                RestaurantUomPolicy::class,
                RestaurantItemPolicy::class,
                RestaurantWarehousePolicy::class,
                RestaurantPurchaseRequestPolicy::class,
                RestaurantPurchaseOrderPolicy::class,
                RestaurantGoodsReceiptPolicy::class,
                RestaurantInventoryDocPolicy::class,
                RestaurantRecipePolicy::class,
                RestaurantMenuItemPolicy::class,
                RestaurantMenuSalePolicy::class,
            ],
            [
                'restaurant' => 'عملیات رستوران',
                'restaurant_master' => 'اطلاعات پایه',
                'restaurant_procurement' => 'خرید',
                'restaurant_inventory' => 'انبار',
                'restaurant_cost' => 'کاست‌کنترل',
                'restaurant_report' => 'گزارش‌ها',
            ]
        );

        self::$registered = true;
    }

    /**
     * @return array<int, string>
     */
    public static function permissions(): array
    {
        return [
            'restaurant.view',
            'restaurant.supplier.view',
            'restaurant.supplier.manage',
            'restaurant.uom.view',
            'restaurant.uom.manage',
            'restaurant.item.view',
            'restaurant.item.manage',
            'restaurant.warehouse.view',
            'restaurant.warehouse.manage',
            'restaurant.purchase_request.view',
            'restaurant.purchase_request.manage',
            'restaurant.purchase_request.approve',
            'restaurant.purchase_order.view',
            'restaurant.purchase_order.manage',
            'restaurant.purchase_order.approve',
            'restaurant.purchase_order.send',
            'restaurant.goods_receipt.view',
            'restaurant.goods_receipt.manage',
            'restaurant.goods_receipt.post',
            'restaurant.inventory_doc.view',
            'restaurant.inventory_doc.manage',
            'restaurant.inventory_doc.post',
            'restaurant.recipe.view',
            'restaurant.recipe.manage',
            'restaurant.menu_item.view',
            'restaurant.menu_item.manage',
            'restaurant.menu_sale.view',
            'restaurant.menu_sale.manage',
            'restaurant.menu_sale.post',
            'restaurant.report.view',
            'restaurant.report.export',
        ];
    }
}
