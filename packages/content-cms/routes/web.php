<?php

use Haida\ContentCms\Http\Controllers\Web\PageController;
use Haida\ContentCms\Http\Controllers\Web\SitemapController;
use Illuminate\Support\Facades\Route;

Route::middleware(['web', 'resolve.site', 'require.service:site'])
    ->group(function () {
        Route::get('sitemap.xml', SitemapController::class);
        Route::get('/', [PageController::class, 'home']);

        $reserved = config('content-cms.public.reserved_slugs', []);
        // NOTE: CMS pages may use nested slugs (e.g. "legal/privacy"), so we allow "/" here.
        // But we must never let the CMS catch-all shadow platform routes like "api/*", "admin/*", etc.
        $pattern = '.+';
        if (is_array($reserved) && $reserved !== []) {
            $escaped = array_map(static fn ($slug) => preg_quote((string) $slug, '/'), $reserved);
            // Exclude reserved prefixes, not just exact matches. For example, reserving "api" must
            // exclude both "api" and "api/v1/...".
            $pattern = '^(?!(?:'.implode('|', $escaped).')(?:/|$)).+';
        }

        Route::get('{slug}', [PageController::class, 'show'])
            ->where('slug', $pattern);
    });
