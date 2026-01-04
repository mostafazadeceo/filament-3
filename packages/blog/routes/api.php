<?php

use Filamat\IamSuite\Http\Middleware\ApiAuth;
use Filamat\IamSuite\Http\Middleware\ApiKeyAuth;
use Filamat\IamSuite\Http\Middleware\ResolveTenant;
use Haida\Blog\Http\Controllers\Api\V1\BlogCategoryController;
use Haida\Blog\Http\Controllers\Api\V1\BlogPostController;
use Haida\Blog\Http\Controllers\Api\V1\BlogTagController;
use Haida\Blog\Http\Controllers\Api\V1\OpenApiController;
use Illuminate\Support\Facades\Route;

Route::prefix('api/v1/blog')
    ->middleware([
        'api',
        ApiKeyAuth::class,
        ApiAuth::class,
        ResolveTenant::class,
        'throttle:'.config('blog.api.rate_limit', '60,1'),
    ])
    ->group(function () {
        Route::apiResource('posts', BlogPostController::class)
            ->only(['index', 'show'])
            ->middleware('filamat-iam.scope:blog.post.view');
        Route::apiResource('posts', BlogPostController::class)
            ->only(['store', 'update', 'destroy'])
            ->middleware('filamat-iam.scope:blog.post.manage');

        Route::apiResource('categories', BlogCategoryController::class)
            ->only(['index', 'show'])
            ->middleware('filamat-iam.scope:blog.category.manage');
        Route::apiResource('categories', BlogCategoryController::class)
            ->only(['store', 'update', 'destroy'])
            ->middleware('filamat-iam.scope:blog.category.manage');

        Route::apiResource('tags', BlogTagController::class)
            ->only(['index', 'show'])
            ->middleware('filamat-iam.scope:blog.tag.manage');
        Route::apiResource('tags', BlogTagController::class)
            ->only(['store', 'update', 'destroy'])
            ->middleware('filamat-iam.scope:blog.tag.manage');

        Route::get('openapi', [OpenApiController::class, 'show'])
            ->middleware('filamat-iam.scope:blog.post.view');
    });
