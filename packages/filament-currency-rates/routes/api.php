<?php

use Haida\FilamentCurrencyRates\Http\Controllers\CurrencyRateApiController;
use Illuminate\Support\Facades\Route;

Route::prefix('currency-rates')->group(function () {
    Route::get('/', [CurrencyRateApiController::class, 'index']);
    Route::get('{code}', [CurrencyRateApiController::class, 'show']);
});
