<?php

use Filamat\IamSuite\Http\Middleware\ApiAuth;
use Filamat\IamSuite\Http\Middleware\ApiKeyAuth;
use Filamat\IamSuite\Http\Middleware\ResolveTenant;
use Haida\PaymentsOrchestrator\Http\Controllers\Api\V1\OpenApiController;
use Haida\PaymentsOrchestrator\Http\Controllers\Api\V1\PaymentIntentController;
use Haida\PaymentsOrchestrator\Http\Controllers\Api\V1\WebhookController;
use Illuminate\Support\Facades\Route;

Route::prefix('api/v1/commerce-payments')
    ->middleware([
        'api',
        ApiKeyAuth::class,
        ApiAuth::class,
        ResolveTenant::class,
        'throttle:'.config('payments-orchestrator.api.rate_limit', '60,1'),
    ])
    ->group(function () {
        Route::post('intents', [PaymentIntentController::class, 'store'])
            ->middleware('filamat-iam.scope:commerce.payment.manage');

        Route::get('intents/{intent}', [PaymentIntentController::class, 'show'])
            ->middleware('filamat-iam.scope:commerce.payment.view');

        Route::get('openapi', [OpenApiController::class, 'show'])
            ->middleware('filamat-iam.scope:commerce.payment.view');
    });

Route::post('api/v1/commerce-payments/webhooks/{provider}', WebhookController::class)
    ->middleware([
        'api',
        ResolveTenant::class,
        'throttle:'.config('payments-orchestrator.api.rate_limit', '60,1'),
    ]);
