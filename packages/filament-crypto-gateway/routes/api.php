<?php

declare(strict_types=1);

use Filamat\IamSuite\Http\Middleware\ApiAuth;
use Filamat\IamSuite\Http\Middleware\ApiKeyAuth;
use Filamat\IamSuite\Http\Middleware\ResolveTenant;
use Haida\FilamentCryptoGateway\Http\Controllers\Api\V1\HealthController;
use Haida\FilamentCryptoGateway\Http\Controllers\Api\V1\InvoiceController;
use Haida\FilamentCryptoGateway\Http\Controllers\Api\V1\OpenApiController;
use Haida\FilamentCryptoGateway\Http\Controllers\Api\V1\PayoutController;
use Haida\FilamentCryptoGateway\Http\Controllers\Api\V1\PayoutDestinationController;
use Haida\FilamentCryptoGateway\Http\Controllers\Api\V1\PolicyController;
use Haida\FilamentCryptoGateway\Http\Controllers\Api\V1\RateController;
use Haida\FilamentCryptoGateway\Http\Controllers\Api\V1\ReconcileController;
use Haida\FilamentCryptoGateway\Http\Controllers\Api\V1\WebhookController;
use Illuminate\Support\Facades\Route;

Route::prefix('api/v1/crypto')
    ->middleware([
        'api',
        ApiKeyAuth::class,
        ApiAuth::class,
        ResolveTenant::class,
        'throttle:'.config('filament-crypto-gateway.api.rate_limit', '60,1'),
    ])
    ->group(function (): void {
        Route::post('invoices', [InvoiceController::class, 'store'])
            ->middleware('filamat-iam.scope:crypto.invoices.manage');

        Route::get('invoices/{invoice}', [InvoiceController::class, 'show'])
            ->middleware('filamat-iam.scope:crypto.invoices.view');

        Route::get('invoices/{invoice}/status', [InvoiceController::class, 'status'])
            ->middleware('filamat-iam.scope:crypto.invoices.view');

        Route::post('invoices/{invoice}/refresh', [InvoiceController::class, 'refresh'])
            ->middleware('filamat-iam.scope:crypto.invoices.manage');

        Route::post('payouts', [PayoutController::class, 'store'])
            ->middleware('filamat-iam.scope:crypto.payouts.manage');

        Route::get('payouts/{payout}', [PayoutController::class, 'show'])
            ->middleware('filamat-iam.scope:crypto.payouts.view');

        Route::post('payouts/{payout}/approve', [PayoutController::class, 'approve'])
            ->middleware('filamat-iam.scope:crypto.payouts.approve');

        Route::post('payouts/{payout}/reject', [PayoutController::class, 'reject'])
            ->middleware('filamat-iam.scope:crypto.payouts.approve');

        Route::get('payout-destinations', [PayoutDestinationController::class, 'index'])
            ->middleware('filamat-iam.scope:crypto.payout_destinations.view');

        Route::post('payout-destinations', [PayoutDestinationController::class, 'store'])
            ->middleware('filamat-iam.scope:crypto.payout_destinations.manage');

        Route::get('payout-destinations/{destination}', [PayoutDestinationController::class, 'show'])
            ->middleware('filamat-iam.scope:crypto.payout_destinations.view');

        Route::put('payout-destinations/{destination}', [PayoutDestinationController::class, 'update'])
            ->middleware('filamat-iam.scope:crypto.payout_destinations.manage');

        Route::delete('payout-destinations/{destination}', [PayoutDestinationController::class, 'destroy'])
            ->middleware('filamat-iam.scope:crypto.payout_destinations.manage');

        Route::post('webhooks/{provider}', [WebhookController::class, 'handle'])
            ->middleware('filamat-iam.scope:crypto.webhooks.manage');

        Route::get('rates', [RateController::class, 'index'])
            ->middleware('filamat-iam.scope:crypto.rates.view');

        Route::get('policy', [PolicyController::class, 'show'])
            ->middleware('filamat-iam.scope:crypto.fee_policies.view');

        Route::get('health/providers', [HealthController::class, 'providers'])
            ->middleware('filamat-iam.scope:crypto.providers.manage');

        Route::get('health/nodes', [HealthController::class, 'nodes'])
            ->middleware('filamat-iam.scope:crypto.nodes.view');

        Route::post('reconcile/run', [ReconcileController::class, 'run'])
            ->middleware('filamat-iam.scope:crypto.reconcile.run');

        Route::get('openapi', [OpenApiController::class, 'show'])
            ->middleware('filamat-iam.scope:crypto.invoices.view');
    });
