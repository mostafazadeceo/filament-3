<?php

use Filamat\IamSuite\Http\Middleware\ApiAuth;
use Filamat\IamSuite\Http\Middleware\ApiKeyAuth;
use Filamat\IamSuite\Http\Middleware\ResolveTenant;
use Haida\FilamentPayments\Http\Controllers\Api\V1\OpenApiController;
use Haida\FilamentPayments\Http\Controllers\Api\V1\PaymentIntentController;
use Haida\FilamentPayments\Http\Controllers\Api\V1\WebhookController;
use Illuminate\Support\Facades\Route;

Route::prefix('api/v1/filament-payments')
    ->middleware([
        'api',
        ApiKeyAuth::class,
        ApiAuth::class,
        ResolveTenant::class,
        'throttle:'.config('filament-payments.api.rate_limit', '60,1'),
    ])
    ->group(function () {
        Route::post('intents', [PaymentIntentController::class, 'store'])
            ->middleware('filamat-iam.scope:payments.manage');

        Route::get('intents/{intent}', [PaymentIntentController::class, 'show'])
            ->middleware('filamat-iam.scope:payments.view');

        Route::post('webhooks/{provider}', [WebhookController::class, 'handle'])
            ->middleware('filamat-iam.scope:payments.webhooks.manage');

        Route::get('openapi', [OpenApiController::class, 'show'])
            ->middleware('filamat-iam.scope:payments.view');
    });
