<?php

use Filamat\IamSuite\Http\Middleware\ApiAuth;
use Filamat\IamSuite\Http\Middleware\ApiKeyAuth;
use Filamat\IamSuite\Http\Middleware\ResolveTenant;
use Haida\FilamentChat\Http\Controllers\Api\V1\ChatConnectionController;
use Illuminate\Support\Facades\Route;

Route::prefix('api/v1/chat')
    ->middleware([
        'api',
        ApiKeyAuth::class,
        ApiAuth::class,
        ResolveTenant::class,
        'throttle:'.config('filament-chat.api.rate_limit', '60,1'),
    ])
    ->group(function () {
        Route::post('connections/{connection}/test', [ChatConnectionController::class, 'test'])
            ->middleware('filamat-iam.scope:chat.connection.view');
        Route::post('connections/{connection}/sync', [ChatConnectionController::class, 'sync'])
            ->middleware('filamat-iam.scope:chat.sync');
        Route::post('connections/{connection}/users/{user}/sync', [ChatConnectionController::class, 'syncUser'])
            ->middleware('filamat-iam.scope:chat.sync');
    });
