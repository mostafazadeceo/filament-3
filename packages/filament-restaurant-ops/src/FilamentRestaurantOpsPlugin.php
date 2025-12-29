<?php

namespace Haida\FilamentRestaurantOps;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Haida\FilamentRestaurantOps\Filament\Resources\RestaurantGoodsReceiptResource;
use Haida\FilamentRestaurantOps\Filament\Resources\RestaurantInventoryDocResource;
use Haida\FilamentRestaurantOps\Filament\Resources\RestaurantItemResource;
use Haida\FilamentRestaurantOps\Filament\Resources\RestaurantMenuItemResource;
use Haida\FilamentRestaurantOps\Filament\Resources\RestaurantMenuSaleResource;
use Haida\FilamentRestaurantOps\Filament\Resources\RestaurantPurchaseOrderResource;
use Haida\FilamentRestaurantOps\Filament\Resources\RestaurantPurchaseRequestResource;
use Haida\FilamentRestaurantOps\Filament\Resources\RestaurantRecipeResource;
use Haida\FilamentRestaurantOps\Filament\Resources\RestaurantSupplierResource;
use Haida\FilamentRestaurantOps\Filament\Resources\RestaurantUomResource;
use Haida\FilamentRestaurantOps\Filament\Resources\RestaurantWarehouseResource;

class FilamentRestaurantOpsPlugin implements Plugin
{
    public static function make(): static
    {
        return app(static::class);
    }

    public function getId(): string
    {
        return 'restaurant-ops';
    }

    public function register(Panel $panel): void
    {
        $panel->resources([
            RestaurantSupplierResource::class,
            RestaurantUomResource::class,
            RestaurantItemResource::class,
            RestaurantWarehouseResource::class,
            RestaurantPurchaseRequestResource::class,
            RestaurantPurchaseOrderResource::class,
            RestaurantGoodsReceiptResource::class,
            RestaurantInventoryDocResource::class,
            RestaurantRecipeResource::class,
            RestaurantMenuItemResource::class,
            RestaurantMenuSaleResource::class,
        ]);
    }

    public function boot(Panel $panel): void
    {
        // No-op.
    }
}
