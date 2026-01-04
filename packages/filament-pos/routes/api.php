<?php

use Filamat\IamSuite\Http\Middleware\ApiAuth;
use Filamat\IamSuite\Http\Middleware\ApiKeyAuth;
use Filamat\IamSuite\Http\Middleware\ResolveTenant;
use Haida\FilamentPos\Http\Controllers\Api\V1\OpenApiController;
use Haida\FilamentPos\Http\Controllers\Api\V1\OutboxController;
use Haida\FilamentPos\Http\Controllers\Api\V1\PosSaleController;
use Haida\FilamentPos\Http\Controllers\Api\V1\SyncController;
use Illuminate\Support\Facades\Route;

Route::prefix('api/v1/filament-pos')
    ->middleware([
        'api',
        ApiKeyAuth::class,
        ApiAuth::class,
        ResolveTenant::class,
        'throttle:'.config('filament-pos.api.rate_limit', '60,1'),
    ])
    ->group(function () {
        Route::get('sync/snapshot', [SyncController::class, 'snapshot'])
            ->middleware('filamat-iam.scope:pos.use');

        Route::get('sync/delta', [SyncController::class, 'delta'])
            ->middleware('filamat-iam.scope:pos.use');

        Route::post('sync/outbox', [OutboxController::class, 'upload'])
            ->middleware('filamat-iam.scope:pos.use');

        Route::post('sales', [PosSaleController::class, 'store'])
            ->middleware('filamat-iam.scope:pos.use');

        Route::get('openapi', [OpenApiController::class, 'show'])
            ->middleware('filamat-iam.scope:pos.view');
    });
