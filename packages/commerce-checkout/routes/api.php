<?php

use Filamat\IamSuite\Http\Middleware\ApiAuth;
use Filamat\IamSuite\Http\Middleware\ApiKeyAuth;
use Filamat\IamSuite\Http\Middleware\ResolveTenant;
use Haida\CommerceCheckout\Http\Controllers\Api\V1\CartController;
use Haida\CommerceCheckout\Http\Controllers\Api\V1\CartItemController;
use Haida\CommerceCheckout\Http\Controllers\Api\V1\CheckoutController;
use Haida\CommerceCheckout\Http\Controllers\Api\V1\OpenApiController;
use Illuminate\Support\Facades\Route;

Route::prefix('api/v1/commerce-checkout')
    ->middleware([
        'api',
        ApiKeyAuth::class,
        ApiAuth::class,
        ResolveTenant::class,
        'throttle:'.config('commerce-checkout.api.rate_limit', '60,1'),
    ])
    ->group(function () {
        Route::apiResource('carts', CartController::class)
            ->only(['index', 'show'])
            ->middleware('filamat-iam.scope:commerce.cart.view');

        Route::apiResource('carts', CartController::class)
            ->only(['store'])
            ->middleware('filamat-iam.scope:commerce.cart.manage');

        Route::post('carts/{cart}/items', [CartItemController::class, 'store'])
            ->middleware('filamat-iam.scope:commerce.cart.manage');

        Route::patch('cart-items/{item}', [CartItemController::class, 'update'])
            ->middleware('filamat-iam.scope:commerce.cart.manage');

        Route::delete('cart-items/{item}', [CartItemController::class, 'destroy'])
            ->middleware('filamat-iam.scope:commerce.cart.manage');

        Route::post('checkout', [CheckoutController::class, 'store'])
            ->middleware('filamat-iam.scope:commerce.checkout.create');

        Route::get('openapi', [OpenApiController::class, 'show'])
            ->middleware('filamat-iam.scope:commerce.cart.view');
    });
