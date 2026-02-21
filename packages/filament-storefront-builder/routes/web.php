<?php

use Haida\FilamentStorefrontBuilder\Http\Controllers\Web\PublicBlockController;
use Haida\FilamentStorefrontBuilder\Http\Controllers\Web\PublicMenuController;
use Haida\FilamentStorefrontBuilder\Http\Controllers\Web\PublicPageController;
use Haida\FilamentStorefrontBuilder\Http\Controllers\Web\PublicThemeController;
use Haida\FilamentStorefrontBuilder\Http\Controllers\Web\SitemapController;
use Illuminate\Support\Facades\Route;

$prefix = config('filament-storefront-builder.public.prefix', 'storefront');

Route::middleware(['web', 'resolve.site', 'require.service:storefront'])
    ->prefix($prefix)
    ->group(function () {
        Route::get('sitemap.xml', SitemapController::class);
        Route::get('pages/{slug}', [PublicPageController::class, 'show']);
        Route::get('menus/{key}', [PublicMenuController::class, 'show']);
        Route::get('blocks/{key}', [PublicBlockController::class, 'show']);
        Route::get('theme', [PublicThemeController::class, 'show']);
    });
