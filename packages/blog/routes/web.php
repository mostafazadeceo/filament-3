<?php

use Haida\Blog\Http\Controllers\Web\BlogController;
use Illuminate\Support\Facades\Route;

$prefix = config('blog.public.prefix', 'blog');

Route::middleware(['web', 'resolve.site', 'require.service:blog'])
    ->group(function () use ($prefix) {
        Route::get($prefix, [BlogController::class, 'index']);
        Route::get($prefix.'/{slug}', [BlogController::class, 'show']);
    });
