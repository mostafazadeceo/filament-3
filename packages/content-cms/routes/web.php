<?php

use Haida\ContentCms\Http\Controllers\Web\PageController;
use Haida\ContentCms\Http\Controllers\Web\SitemapController;
use Illuminate\Support\Facades\Route;

Route::middleware(['web', 'resolve.site'])
    ->group(function () {
        Route::get('sitemap.xml', SitemapController::class);
        Route::get('/', [PageController::class, 'home']);

        $reserved = config('content-cms.public.reserved_slugs', []);
        $pattern = '.+';
        if (is_array($reserved) && $reserved !== []) {
            $escaped = array_map(static fn ($slug) => preg_quote((string) $slug, '/'), $reserved);
            $pattern = '^(?!('.implode('|', $escaped).')$).+';
        }

        Route::get('{slug}', [PageController::class, 'show'])
            ->where('slug', $pattern);
    });
