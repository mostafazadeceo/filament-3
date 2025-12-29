<?php

use Filamat\IamSuite\Http\Middleware\ApiAuth;
use Filamat\IamSuite\Http\Middleware\ApiKeyAuth;
use Filamat\IamSuite\Http\Middleware\ResolveTenant;
use Haida\FilamentPettyCashIr\Http\Controllers\Api\V1\CategoryController;
use Haida\FilamentPettyCashIr\Http\Controllers\Api\V1\ExpenseController;
use Haida\FilamentPettyCashIr\Http\Controllers\Api\V1\FundController;
use Haida\FilamentPettyCashIr\Http\Controllers\Api\V1\OpenApiController;
use Haida\FilamentPettyCashIr\Http\Controllers\Api\V1\ReplenishmentController;
use Haida\FilamentPettyCashIr\Http\Controllers\Api\V1\SettlementController;
use Illuminate\Support\Facades\Route;

Route::prefix('api/v1/petty-cash')
    ->middleware([
        'api',
        ApiKeyAuth::class,
        ApiAuth::class,
        ResolveTenant::class,
        'throttle:'.config('filament-petty-cash-ir.api.rate_limit', '60,1'),
    ])
    ->group(function () {
        Route::apiResource('funds', FundController::class)
            ->only(['index', 'show'])
            ->middleware('filamat-iam.scope:petty_cash.fund.view');
        Route::apiResource('funds', FundController::class)
            ->only(['store', 'update', 'destroy'])
            ->middleware('filamat-iam.scope:petty_cash.fund.manage');

        Route::apiResource('categories', CategoryController::class)
            ->only(['index', 'show'])
            ->middleware('filamat-iam.scope:petty_cash.category.view');
        Route::apiResource('categories', CategoryController::class)
            ->only(['store', 'update', 'destroy'])
            ->middleware('filamat-iam.scope:petty_cash.category.manage');

        Route::apiResource('expenses', ExpenseController::class)
            ->only(['index', 'show'])
            ->middleware('filamat-iam.scope:petty_cash.expense.view');
        Route::apiResource('expenses', ExpenseController::class)
            ->only(['store', 'update', 'destroy'])
            ->middleware('filamat-iam.scope:petty_cash.expense.manage');
        Route::post('expenses/{expense}/submit', [ExpenseController::class, 'submit'])
            ->middleware('filamat-iam.scope:petty_cash.expense.manage');
        Route::post('expenses/{expense}/approve', [ExpenseController::class, 'approve'])
            ->middleware('filamat-iam.scope:petty_cash.expense.approve');
        Route::post('expenses/{expense}/reject', [ExpenseController::class, 'reject'])
            ->middleware('filamat-iam.scope:petty_cash.expense.reject');
        Route::post('expenses/{expense}/post', [ExpenseController::class, 'post'])
            ->middleware('filamat-iam.scope:petty_cash.expense.post');

        Route::apiResource('replenishments', ReplenishmentController::class)
            ->only(['index', 'show'])
            ->parameters(['replenishments' => 'replenishment'])
            ->middleware('filamat-iam.scope:petty_cash.replenishment.view');
        Route::apiResource('replenishments', ReplenishmentController::class)
            ->only(['store', 'update', 'destroy'])
            ->parameters(['replenishments' => 'replenishment'])
            ->middleware('filamat-iam.scope:petty_cash.replenishment.manage');
        Route::post('replenishments/{replenishment}/submit', [ReplenishmentController::class, 'submit'])
            ->middleware('filamat-iam.scope:petty_cash.replenishment.manage');
        Route::post('replenishments/{replenishment}/approve', [ReplenishmentController::class, 'approve'])
            ->middleware('filamat-iam.scope:petty_cash.replenishment.approve');
        Route::post('replenishments/{replenishment}/reject', [ReplenishmentController::class, 'reject'])
            ->middleware('filamat-iam.scope:petty_cash.replenishment.reject');
        Route::post('replenishments/{replenishment}/post', [ReplenishmentController::class, 'post'])
            ->middleware('filamat-iam.scope:petty_cash.replenishment.post');

        Route::apiResource('settlements', SettlementController::class)
            ->only(['index', 'show'])
            ->parameters(['settlements' => 'settlement'])
            ->middleware('filamat-iam.scope:petty_cash.settlement.view');
        Route::apiResource('settlements', SettlementController::class)
            ->only(['store', 'update', 'destroy'])
            ->parameters(['settlements' => 'settlement'])
            ->middleware('filamat-iam.scope:petty_cash.settlement.manage');
        Route::post('settlements/{settlement}/submit', [SettlementController::class, 'submit'])
            ->middleware('filamat-iam.scope:petty_cash.settlement.manage');
        Route::post('settlements/{settlement}/approve', [SettlementController::class, 'approve'])
            ->middleware('filamat-iam.scope:petty_cash.settlement.approve');
        Route::post('settlements/{settlement}/post', [SettlementController::class, 'post'])
            ->middleware('filamat-iam.scope:petty_cash.settlement.post');

        Route::get('openapi', [OpenApiController::class, 'show']);
    });
