<?php

use Filamat\IamSuite\Http\Middleware\ApiAuth;
use Filamat\IamSuite\Http\Middleware\ApiKeyAuth;
use Filamat\IamSuite\Http\Middleware\ResolveTenant;
use Haida\FilamentStorefrontBuilder\Http\Controllers\Api\V1\OpenApiController;
use Haida\FilamentStorefrontBuilder\Http\Controllers\Web\PublicBlockController;
use Haida\FilamentStorefrontBuilder\Http\Controllers\Web\PublicMenuController;
use Haida\FilamentStorefrontBuilder\Http\Controllers\Web\PublicPageController;
use Haida\FilamentStorefrontBuilder\Http\Controllers\Web\PublicThemeController;
use Haida\FilamentStorefrontBuilder\Http\Controllers\Web\SitemapController;
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
        // Read-only public data endpoints (tenant-scoped via API key / X-Tenant-ID).
        Route::get('theme', [PublicThemeController::class, 'show'])
            ->middleware('filamat-iam.scope:storebuilder.view');
        Route::get('menus/{key}', [PublicMenuController::class, 'show'])
            ->middleware('filamat-iam.scope:storebuilder.view');
        Route::get('blocks/{key}', [PublicBlockController::class, 'show'])
            ->middleware('filamat-iam.scope:storebuilder.view');
        // Supports optional ?site_id=... (for multi-site tenants).
        Route::get('pages/{slug}', [PublicPageController::class, 'show'])
            ->middleware('filamat-iam.scope:storebuilder.view');
        Route::get('sitemap.xml', SitemapController::class)
            ->middleware('filamat-iam.scope:storebuilder.view');

        Route::get('openapi', [OpenApiController::class, 'show'])
            ->middleware('filamat-iam.scope:storebuilder.view');
    });
