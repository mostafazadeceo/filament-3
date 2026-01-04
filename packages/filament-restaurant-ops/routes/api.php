<?php

use Filamat\IamSuite\Http\Middleware\ApiAuth;
use Filamat\IamSuite\Http\Middleware\ApiKeyAuth;
use Filamat\IamSuite\Http\Middleware\ResolveTenant;
use Haida\FilamentRestaurantOps\Http\Controllers\Api\V1\GoodsReceiptController;
use Haida\FilamentRestaurantOps\Http\Controllers\Api\V1\InventoryDocController;
use Haida\FilamentRestaurantOps\Http\Controllers\Api\V1\ItemController;
use Haida\FilamentRestaurantOps\Http\Controllers\Api\V1\MenuItemController;
use Haida\FilamentRestaurantOps\Http\Controllers\Api\V1\MenuSaleController;
use Haida\FilamentRestaurantOps\Http\Controllers\Api\V1\OpenApiController;
use Haida\FilamentRestaurantOps\Http\Controllers\Api\V1\PurchaseOrderController;
use Haida\FilamentRestaurantOps\Http\Controllers\Api\V1\PurchaseRequestController;
use Haida\FilamentRestaurantOps\Http\Controllers\Api\V1\RecipeController;
use Haida\FilamentRestaurantOps\Http\Controllers\Api\V1\SupplierController;
use Haida\FilamentRestaurantOps\Http\Controllers\Api\V1\UomController;
use Haida\FilamentRestaurantOps\Http\Controllers\Api\V1\WarehouseController;
use Illuminate\Support\Facades\Route;

Route::prefix('api/v1/restaurant-ops')
    ->middleware([
        'api',
        ApiKeyAuth::class,
        ApiAuth::class,
        ResolveTenant::class,
        'throttle:'.config('filament-restaurant-ops.api.rate_limit', '60,1'),
    ])
    ->group(function () {
        Route::apiResource('suppliers', SupplierController::class)
            ->only(['index', 'show'])
            ->middleware('filamat-iam.scope:restaurant.supplier.view');
        Route::apiResource('suppliers', SupplierController::class)
            ->only(['store', 'update', 'destroy'])
            ->middleware('filamat-iam.scope:restaurant.supplier.manage');

        Route::apiResource('uoms', UomController::class)
            ->only(['index', 'show'])
            ->parameters(['uoms' => 'uom'])
            ->middleware('filamat-iam.scope:restaurant.uom.view');
        Route::apiResource('uoms', UomController::class)
            ->only(['store', 'update', 'destroy'])
            ->parameters(['uoms' => 'uom'])
            ->middleware('filamat-iam.scope:restaurant.uom.manage');

        Route::apiResource('items', ItemController::class)
            ->only(['index', 'show'])
            ->parameters(['items' => 'item'])
            ->middleware('filamat-iam.scope:restaurant.item.view');
        Route::apiResource('items', ItemController::class)
            ->only(['store', 'update', 'destroy'])
            ->parameters(['items' => 'item'])
            ->middleware('filamat-iam.scope:restaurant.item.manage');

        Route::apiResource('warehouses', WarehouseController::class)
            ->only(['index', 'show'])
            ->parameters(['warehouses' => 'warehouse'])
            ->middleware('filamat-iam.scope:restaurant.warehouse.view');
        Route::apiResource('warehouses', WarehouseController::class)
            ->only(['store', 'update', 'destroy'])
            ->parameters(['warehouses' => 'warehouse'])
            ->middleware('filamat-iam.scope:restaurant.warehouse.manage');

        Route::apiResource('purchase-requests', PurchaseRequestController::class)
            ->only(['index', 'show'])
            ->parameters(['purchase-requests' => 'purchase_request'])
            ->middleware('filamat-iam.scope:restaurant.purchase_request.view');
        Route::apiResource('purchase-requests', PurchaseRequestController::class)
            ->only(['store', 'update', 'destroy'])
            ->parameters(['purchase-requests' => 'purchase_request'])
            ->middleware('filamat-iam.scope:restaurant.purchase_request.manage');

        Route::apiResource('purchase-orders', PurchaseOrderController::class)
            ->only(['index', 'show'])
            ->parameters(['purchase-orders' => 'purchase_order'])
            ->middleware('filamat-iam.scope:restaurant.purchase_order.view');
        Route::apiResource('purchase-orders', PurchaseOrderController::class)
            ->only(['store', 'update', 'destroy'])
            ->parameters(['purchase-orders' => 'purchase_order'])
            ->middleware('filamat-iam.scope:restaurant.purchase_order.manage');

        Route::apiResource('goods-receipts', GoodsReceiptController::class)
            ->only(['index', 'show'])
            ->parameters(['goods-receipts' => 'goods_receipt'])
            ->middleware('filamat-iam.scope:restaurant.goods_receipt.view');
        Route::apiResource('goods-receipts', GoodsReceiptController::class)
            ->only(['store', 'update', 'destroy'])
            ->parameters(['goods-receipts' => 'goods_receipt'])
            ->middleware('filamat-iam.scope:restaurant.goods_receipt.manage');
        Route::post('goods-receipts/{goods_receipt}/post', [GoodsReceiptController::class, 'post'])
            ->middleware('filamat-iam.scope:restaurant.goods_receipt.post');

        Route::apiResource('inventory-docs', InventoryDocController::class)
            ->only(['index', 'show'])
            ->parameters(['inventory-docs' => 'inventory_doc'])
            ->middleware('filamat-iam.scope:restaurant.inventory_doc.view');
        Route::apiResource('inventory-docs', InventoryDocController::class)
            ->only(['store', 'update', 'destroy'])
            ->parameters(['inventory-docs' => 'inventory_doc'])
            ->middleware('filamat-iam.scope:restaurant.inventory_doc.manage');
        Route::post('inventory-docs/{inventory_doc}/post', [InventoryDocController::class, 'post'])
            ->middleware('filamat-iam.scope:restaurant.inventory_doc.post');

        Route::apiResource('recipes', RecipeController::class)
            ->only(['index', 'show'])
            ->parameters(['recipes' => 'recipe'])
            ->middleware('filamat-iam.scope:restaurant.recipe.view');
        Route::apiResource('recipes', RecipeController::class)
            ->only(['store', 'update', 'destroy'])
            ->parameters(['recipes' => 'recipe'])
            ->middleware('filamat-iam.scope:restaurant.recipe.manage');

        Route::apiResource('menu-items', MenuItemController::class)
            ->only(['index', 'show'])
            ->parameters(['menu-items' => 'menu_item'])
            ->middleware('filamat-iam.scope:restaurant.menu_item.view');
        Route::apiResource('menu-items', MenuItemController::class)
            ->only(['store', 'update', 'destroy'])
            ->parameters(['menu-items' => 'menu_item'])
            ->middleware('filamat-iam.scope:restaurant.menu_item.manage');

        Route::apiResource('menu-sales', MenuSaleController::class)
            ->only(['index', 'show'])
            ->parameters(['menu-sales' => 'menu_sale'])
            ->middleware('filamat-iam.scope:restaurant.menu_sale.view');
        Route::apiResource('menu-sales', MenuSaleController::class)
            ->only(['store', 'update', 'destroy'])
            ->parameters(['menu-sales' => 'menu_sale'])
            ->middleware('filamat-iam.scope:restaurant.menu_sale.manage');
        Route::post('menu-sales/{menu_sale}/post', [MenuSaleController::class, 'post'])
            ->middleware('filamat-iam.scope:restaurant.menu_sale.post');

        Route::get('openapi', [OpenApiController::class, 'show'])
            ->middleware('filamat-iam.scope:restaurant.view');
    });
