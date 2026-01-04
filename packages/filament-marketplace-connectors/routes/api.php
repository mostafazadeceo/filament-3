<?php

use Filamat\IamSuite\Http\Middleware\ApiAuth;
use Filamat\IamSuite\Http\Middleware\ApiKeyAuth;
use Filamat\IamSuite\Http\Middleware\ResolveTenant;
use Haida\FilamentMarketplaceConnectors\Http\Controllers\Api\V1\ConnectorController;
use Haida\FilamentMarketplaceConnectors\Http\Controllers\Api\V1\OpenApiController;
use Haida\FilamentMarketplaceConnectors\Http\Controllers\Api\V1\SyncController;
use Illuminate\Support\Facades\Route;

Route::prefix('api/v1/filament-marketplace-connectors')
    ->middleware([
        'api',
        ApiKeyAuth::class,
        ApiAuth::class,
        ResolveTenant::class,
        'throttle:'.config('filament-marketplace-connectors.api.rate_limit', '60,1'),
    ])
    ->group(function () {
        Route::get('connectors', [ConnectorController::class, 'index'])
            ->middleware('filamat-iam.scope:marketplace.connectors.manage');

        Route::post('connectors/{connector}/sync', [SyncController::class, 'store'])
            ->middleware('filamat-iam.scope:marketplace.connectors.sync');

        Route::get('openapi', [OpenApiController::class, 'show'])
            ->middleware('filamat-iam.scope:marketplace.connectors.manage');
    });
