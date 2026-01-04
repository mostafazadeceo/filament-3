<?php

use Filamat\IamSuite\Http\Middleware\ApiAuth;
use Filamat\IamSuite\Http\Middleware\ApiKeyAuth;
use Filamat\IamSuite\Http\Middleware\ResolveTenant;
use Haida\ContentCms\Http\Controllers\Api\V1\OpenApiController;
use Haida\ContentCms\Http\Controllers\Api\V1\PageController;
use Illuminate\Support\Facades\Route;

Route::prefix('api/v1/content-cms')
    ->middleware([
        'api',
        ApiKeyAuth::class,
        ApiAuth::class,
        ResolveTenant::class,
        'throttle:'.config('content-cms.api.rate_limit', '60,1'),
    ])
    ->group(function () {
        Route::apiResource('pages', PageController::class)
            ->only(['index', 'show'])
            ->middleware('filamat-iam.scope:cms.page.view');
        Route::apiResource('pages', PageController::class)
            ->only(['store', 'update', 'destroy'])
            ->middleware('filamat-iam.scope:cms.page.manage');

        Route::get('openapi', [OpenApiController::class, 'show'])
            ->middleware('filamat-iam.scope:cms.page.view');
    });
