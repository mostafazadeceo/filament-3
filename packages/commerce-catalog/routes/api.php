<?php

use Filamat\IamSuite\Http\Middleware\ApiAuth;
use Filamat\IamSuite\Http\Middleware\ApiKeyAuth;
use Filamat\IamSuite\Http\Middleware\ResolveTenant;
use Haida\CommerceCatalog\Http\Controllers\Api\V1\CollectionController;
use Haida\CommerceCatalog\Http\Controllers\Api\V1\OpenApiController;
use Haida\CommerceCatalog\Http\Controllers\Api\V1\ProductController;
use Illuminate\Support\Facades\Route;

Route::prefix('api/v1/commerce-catalog')
    ->middleware([
        'api',
        ApiKeyAuth::class,
        ApiAuth::class,
        ResolveTenant::class,
        'throttle:'.config('commerce-catalog.api.rate_limit', '60,1'),
    ])
    ->group(function () {
        Route::apiResource('products', ProductController::class)
            ->only(['index', 'show'])
            ->middleware('filamat-iam.scope:catalog.product.view');
        Route::apiResource('products', ProductController::class)
            ->only(['store', 'update', 'destroy'])
            ->middleware('filamat-iam.scope:catalog.product.manage');

        Route::apiResource('collections', CollectionController::class)
            ->only(['index', 'show'])
            ->middleware('filamat-iam.scope:catalog.collection.manage');
        Route::apiResource('collections', CollectionController::class)
            ->only(['store', 'update', 'destroy'])
            ->middleware('filamat-iam.scope:catalog.collection.manage');

        Route::get('openapi', [OpenApiController::class, 'show'])
            ->middleware('filamat-iam.scope:catalog.product.view');
    });
