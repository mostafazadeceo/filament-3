<?php

use Filamat\IamSuite\Http\Middleware\ApiAuth;
use Filamat\IamSuite\Http\Middleware\ApiKeyAuth;
use Filamat\IamSuite\Http\Middleware\ResolveTenant;
use Haida\FilamentStorefrontBuilder\Http\Controllers\Api\V1\OpenApiController;
use Illuminate\Support\Facades\Route;

Route::prefix('api/v1/filament-storefront-builder')
    ->middleware([
        'api',
        ApiKeyAuth::class,
        ApiAuth::class,
        ResolveTenant::class,
        'throttle:'.config('filament-storefront-builder.api.rate_limit', '60,1'),
    ])
    ->group(function () {
        Route::get('openapi', [OpenApiController::class, 'show'])
            ->middleware('filamat-iam.scope:storebuilder.view');
    });
