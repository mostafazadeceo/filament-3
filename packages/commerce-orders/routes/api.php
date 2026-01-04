<?php

use Filamat\IamSuite\Http\Middleware\ApiAuth;
use Filamat\IamSuite\Http\Middleware\ApiKeyAuth;
use Filamat\IamSuite\Http\Middleware\ResolveTenant;
use Haida\CommerceOrders\Http\Controllers\Api\V1\OpenApiController;
use Haida\CommerceOrders\Http\Controllers\Api\V1\OrderController;
use Illuminate\Support\Facades\Route;

Route::prefix('api/v1/commerce-orders')
    ->middleware([
        'api',
        ApiKeyAuth::class,
        ApiAuth::class,
        ResolveTenant::class,
        'throttle:'.config('commerce-orders.api.rate_limit', '60,1'),
    ])
    ->group(function () {
        Route::apiResource('orders', OrderController::class)
            ->only(['index', 'show'])
            ->middleware('filamat-iam.scope:commerce.order.view');

        Route::apiResource('orders', OrderController::class)
            ->only(['update'])
            ->middleware('filamat-iam.scope:commerce.order.manage');

        Route::get('openapi', [OpenApiController::class, 'show'])
            ->middleware('filamat-iam.scope:commerce.order.view');
    });
