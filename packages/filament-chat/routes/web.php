<?php

use Haida\FilamentChat\Http\Controllers\Web\ChatRedirectController;
use Illuminate\Support\Facades\Route;

Route::middleware(['web', 'resolve.site', 'require.service:chat'])
    ->prefix('chat')
    ->group(function () {
        Route::get('/', ChatRedirectController::class);
        Route::get('{any}', ChatRedirectController::class)->where('any', '.*');
    });
