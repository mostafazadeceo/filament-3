<?php

use Filamat\IamSuite\Http\Controllers\Api\V1\GroupController;
use Filamat\IamSuite\Http\Controllers\Api\V1\NotificationController;
use Filamat\IamSuite\Http\Controllers\Api\V1\PermissionController;
use Filamat\IamSuite\Http\Controllers\Api\V1\RoleController;
use Filamat\IamSuite\Http\Controllers\Api\V1\SubscriptionController;
use Filamat\IamSuite\Http\Controllers\Api\V1\SubscriptionPlanController;
use Filamat\IamSuite\Http\Controllers\Api\V1\TenantController;
use Filamat\IamSuite\Http\Controllers\Api\V1\UserController;
use Filamat\IamSuite\Http\Controllers\Api\V1\WalletController;
use Filamat\IamSuite\Http\Controllers\Api\V1\WalletHoldController;
use Filamat\IamSuite\Http\Controllers\Api\V1\WalletTransactionController;
use Filamat\IamSuite\Http\Middleware\ApiAuth;
use Filamat\IamSuite\Http\Middleware\ApiKeyAuth;
use Filamat\IamSuite\Http\Middleware\ResolveTenant;
use Illuminate\Support\Facades\Route;

Route::prefix('api/v1')
    ->middleware(['api', ApiKeyAuth::class, ApiAuth::class, ResolveTenant::class, 'throttle:'.config('filamat-iam.api.rate_limit', '60,1')])
    ->group(function () {
        Route::apiResource('tenants', TenantController::class)->middleware('filamat-iam.scope:iam');
        Route::apiResource('users', UserController::class)->middleware('filamat-iam.scope:iam');
        Route::apiResource('roles', RoleController::class)->middleware('filamat-iam.scope:iam');
        Route::apiResource('permissions', PermissionController::class)->middleware('filamat-iam.scope:iam');
        Route::apiResource('groups', GroupController::class)->middleware('filamat-iam.scope:iam');
        Route::apiResource('wallets', WalletController::class)->middleware('filamat-iam.scope:wallet');
        Route::apiResource('transactions', WalletTransactionController::class)->only(['index', 'show'])->middleware('filamat-iam.scope:wallet');
        Route::apiResource('wallet-holds', WalletHoldController::class)->only(['index', 'show'])->middleware('filamat-iam.scope:wallet');
        Route::apiResource('plans', SubscriptionPlanController::class)->middleware('filamat-iam.scope:subscription');
        Route::apiResource('subscriptions', SubscriptionController::class)->middleware('filamat-iam.scope:subscription');

        Route::post('notifications/send', [NotificationController::class, 'send'])->middleware('filamat-iam.scope:notification.send');

        Route::post('wallets/{wallet}/credit', [WalletController::class, 'credit'])->middleware('filamat-iam.scope:wallet');
        Route::post('wallets/{wallet}/debit', [WalletController::class, 'debit'])->middleware('filamat-iam.scope:wallet');
        Route::post('wallets/{wallet}/holds', [WalletHoldController::class, 'store'])->middleware('filamat-iam.scope:wallet');
        Route::post('wallets/transfer', [WalletController::class, 'transfer'])->middleware('filamat-iam.scope:wallet');
        Route::post('wallet-holds/{hold}/capture', [WalletHoldController::class, 'capture'])->middleware('filamat-iam.scope:wallet');
        Route::post('wallet-holds/{hold}/release', [WalletHoldController::class, 'release'])->middleware('filamat-iam.scope:wallet');
    });
