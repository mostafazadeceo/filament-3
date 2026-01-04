<?php

use Haida\FilamentCurrencyRates\Http\Controllers\CurrencyRateApiController;
use Illuminate\Support\Facades\Route;

Route::prefix('currency-rates')
    ->middleware([
        'api',
        'throttle:'.config('currency-rates.api.rate_limit', '60,1'),
    ])
    ->group(function () {
        Route::get('/', [CurrencyRateApiController::class, 'index']);
        Route::get('{code}', [CurrencyRateApiController::class, 'show']);
    });
