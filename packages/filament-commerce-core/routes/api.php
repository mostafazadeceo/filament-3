<?php

use Filamat\IamSuite\Http\Middleware\ApiAuth;
use Filamat\IamSuite\Http\Middleware\ApiKeyAuth;
use Filamat\IamSuite\Http\Middleware\ResolveTenant;
use Haida\FilamentCommerceCore\Http\Controllers\Api\V1\CatalogSnapshotController;
use Haida\FilamentCommerceCore\Http\Controllers\Api\V1\InventorySnapshotController;
use Haida\FilamentCommerceCore\Http\Controllers\Api\V1\OpenApiController;
use Haida\FilamentCommerceCore\Http\Controllers\Api\V1\PricingSnapshotController;
use Illuminate\Support\Facades\Route;

Route::prefix('api/v1/filament-commerce-core')
    ->middleware([
        'api',
        ApiKeyAuth::class,
        ApiAuth::class,
        ResolveTenant::class,
        'throttle:'.config('filament-commerce-core.api.rate_limit', '60,1'),
    ])
    ->group(function () {
        Route::get('snapshots/catalog', [CatalogSnapshotController::class, 'index'])
            ->middleware('filamat-iam.scope:commerce.catalog.view');

        Route::get('snapshots/pricing', [PricingSnapshotController::class, 'index'])
            ->middleware('filamat-iam.scope:commerce.pricing.view');

        Route::get('snapshots/inventory', [InventorySnapshotController::class, 'index'])
            ->middleware('filamat-iam.scope:commerce.inventory.view');

        Route::get('openapi', [OpenApiController::class, 'show'])
            ->middleware('filamat-iam.scope:commerce.catalog.view');
    });
