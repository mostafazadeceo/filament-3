<?php

declare(strict_types=1);

use Filamat\IamSuite\Http\Middleware\ApiAuth;
use Filamat\IamSuite\Http\Middleware\ApiKeyAuth;
use Filamat\IamSuite\Http\Middleware\ResolveTenant;
use Haida\ProvidersEsimGoCore\Http\Controllers\Api\V1\EsimGoConnectionController;
use Haida\ProvidersEsimGoCore\Http\Controllers\Api\V1\EsimGoOrderController;
use Haida\ProvidersEsimGoCore\Http\Controllers\Api\V1\EsimGoProductController;
use Haida\ProvidersEsimGoCore\Http\Controllers\Api\V1\EsimGoSyncController;
use Haida\ProvidersEsimGoCore\Http\Controllers\Api\V1\OpenApiController;
use Illuminate\Support\Facades\Route;

Route::prefix('api/v1/providers/esim-go')
    ->middleware([
        'api',
        ApiKeyAuth::class,
        ApiAuth::class,
        ResolveTenant::class,
        'throttle:'.config('providers-esim-go-core.api.rate_limit', '60,1'),
    ])
    ->group(function () {
        Route::get('connections', [EsimGoConnectionController::class, 'index'])
            ->middleware('filamat-iam.scope:esim_go.connection.view');

        Route::get('products', [EsimGoProductController::class, 'index'])
            ->middleware('filamat-iam.scope:esim_go.product.view');
        Route::get('products/{product}', [EsimGoProductController::class, 'show'])
            ->middleware('filamat-iam.scope:esim_go.product.view');

        Route::get('orders', [EsimGoOrderController::class, 'index'])
            ->middleware('filamat-iam.scope:esim_go.order.view');
        Route::get('orders/{order}', [EsimGoOrderController::class, 'show'])
            ->middleware('filamat-iam.scope:esim_go.order.view');

        Route::post('sync', [EsimGoSyncController::class, 'store'])
            ->middleware('filamat-iam.scope:esim_go.catalogue.sync');

        Route::get('openapi', [OpenApiController::class, 'show'])
            ->middleware('filamat-iam.scope:esim_go.product.view');
    });
