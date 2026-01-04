<?php

use Filamat\IamSuite\Http\Middleware\ApiAuth;
use Filamat\IamSuite\Http\Middleware\ApiKeyAuth;
use Filamat\IamSuite\Http\Middleware\ResolveTenant;
use Haida\FilamentThreeCx\Http\Controllers\Api\V1\CrmController;
use Haida\FilamentThreeCx\Http\Controllers\Api\V1\OpenApiController;
use Haida\FilamentThreeCx\Http\Middleware\ThreeCxCrmAuth;
use Illuminate\Support\Facades\Route;

Route::prefix('api/v1/threecx')
    ->middleware([
        'api',
        ApiKeyAuth::class,
        ApiAuth::class,
        ResolveTenant::class,
        'throttle:'.config('filament-threecx.api.rate_limit', '60,1'),
    ])
    ->group(function () {
        Route::get('openapi', [OpenApiController::class, 'show'])
            ->middleware('filamat-iam.scope:threecx.view');
    });

Route::prefix('api/v1/threecx/crm')
    ->middleware([
        'api',
        ThreeCxCrmAuth::class,
        'throttle:'.config('filament-threecx.crm_connector.rate_limit', '30,1'),
        'filamat-iam.scope:threecx.crm_connector',
    ])
    ->group(function () {
        Route::get('lookup', [CrmController::class, 'lookup']);
        Route::get('search', [CrmController::class, 'search']);
        Route::post('contacts', [CrmController::class, 'storeContact']);
        Route::post('journal/call', [CrmController::class, 'journalCall']);
        Route::post('journal/chat', [CrmController::class, 'journalChat']);
    });
