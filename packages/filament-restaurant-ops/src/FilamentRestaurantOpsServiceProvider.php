<?php

namespace Haida\FilamentRestaurantOps;

use Filamat\IamSuite\Contracts\CapabilityRegistryInterface;
use Filamat\IamSuite\Support\IamAuthorization;
use Haida\FilamentRestaurantOps\Models\RestaurantGoodsReceipt;
use Haida\FilamentRestaurantOps\Models\RestaurantInventoryDoc;
use Haida\FilamentRestaurantOps\Models\RestaurantItem;
use Haida\FilamentRestaurantOps\Models\RestaurantMenuItem;
use Haida\FilamentRestaurantOps\Models\RestaurantMenuSale;
use Haida\FilamentRestaurantOps\Models\RestaurantPurchaseOrder;
use Haida\FilamentRestaurantOps\Models\RestaurantPurchaseRequest;
use Haida\FilamentRestaurantOps\Models\RestaurantRecipe;
use Haida\FilamentRestaurantOps\Models\RestaurantSupplier;
use Haida\FilamentRestaurantOps\Models\RestaurantUom;
use Haida\FilamentRestaurantOps\Models\RestaurantWarehouse;
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
use Haida\FilamentRestaurantOps\Support\RestaurantOpsCapabilities;
use Illuminate\Support\Facades\Gate;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class FilamentRestaurantOpsServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('filament-restaurant-ops')
            ->hasConfigFile('filament-restaurant-ops')
            ->hasTranslations()
            ->hasRoutes('api')
            ->hasMigrations([
                '2025_12_30_000001_create_restaurant_ops_core_tables',
                '2025_12_30_000002_create_restaurant_ops_procurement_tables',
                '2025_12_30_000003_create_restaurant_ops_inventory_tables',
                '2025_12_30_000004_create_restaurant_ops_cost_tables',
                '2025_12_30_000005_add_warehouse_to_menu_sales',
                '2025_12_30_000006_add_accounting_links_to_restaurant_ops',
            ])
            ->runsMigrations();
    }

    public function packageBooted(): void
    {
        Gate::policy(RestaurantSupplier::class, RestaurantSupplierPolicy::class);
        Gate::policy(RestaurantUom::class, RestaurantUomPolicy::class);
        Gate::policy(RestaurantItem::class, RestaurantItemPolicy::class);
        Gate::policy(RestaurantWarehouse::class, RestaurantWarehousePolicy::class);
        Gate::policy(RestaurantPurchaseRequest::class, RestaurantPurchaseRequestPolicy::class);
        Gate::policy(RestaurantPurchaseOrder::class, RestaurantPurchaseOrderPolicy::class);
        Gate::policy(RestaurantGoodsReceipt::class, RestaurantGoodsReceiptPolicy::class);
        Gate::policy(RestaurantInventoryDoc::class, RestaurantInventoryDocPolicy::class);
        Gate::policy(RestaurantRecipe::class, RestaurantRecipePolicy::class);
        Gate::policy(RestaurantMenuItem::class, RestaurantMenuItemPolicy::class);
        Gate::policy(RestaurantMenuSale::class, RestaurantMenuSalePolicy::class);

        if (interface_exists(CapabilityRegistryInterface::class)) {
            $registry = $this->app->make(CapabilityRegistryInterface::class);
            RestaurantOpsCapabilities::register($registry);
        }

        Gate::define('restaurant.view', fn () => IamAuthorization::allows('restaurant.view'));
    }
}
