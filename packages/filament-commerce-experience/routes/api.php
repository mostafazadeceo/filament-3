<?php

use Filamat\IamSuite\Http\Middleware\ApiAuth;
use Filamat\IamSuite\Http\Middleware\ApiKeyAuth;
use Filamat\IamSuite\Http\Middleware\ResolveTenant;
use Haida\FilamentCommerceExperience\Http\Controllers\Api\V1\BuyNowController;
use Haida\FilamentCommerceExperience\Http\Controllers\Api\V1\CsatController;
use Haida\FilamentCommerceExperience\Http\Controllers\Api\V1\OpenApiController;
use Haida\FilamentCommerceExperience\Http\Controllers\Api\V1\QuestionController;
use Haida\FilamentCommerceExperience\Http\Controllers\Api\V1\ReviewController;
use Illuminate\Support\Facades\Route;

Route::prefix('api/v1/filament-commerce-experience')
    ->middleware([
        'api',
        ApiKeyAuth::class,
        ApiAuth::class,
        ResolveTenant::class,
        'throttle:'.config('filament-commerce-experience.api.rate_limit', '60,1'),
    ])
    ->group(function () {
        Route::get('reviews', [ReviewController::class, 'index'])
            ->middleware('filamat-iam.scope:experience.reviews.view');

        Route::get('questions', [QuestionController::class, 'index'])
            ->middleware('filamat-iam.scope:experience.reviews.view');

        Route::post('csat', [CsatController::class, 'store'])
            ->middleware('filamat-iam.scope:experience.csat.manage');

        Route::post('buy-now', [BuyNowController::class, 'store'])
            ->middleware('filamat-iam.scope:experience.buy_now.manage');

        Route::get('openapi', [OpenApiController::class, 'show'])
            ->middleware('filamat-iam.scope:experience.reviews.view');
    });
