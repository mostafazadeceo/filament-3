<?php

declare(strict_types=1);

use Haida\ProvidersEsimGoWebhooks\Http\Controllers\EsimGoWebhookController;
use Illuminate\Support\Facades\Route;

Route::prefix(config('providers-esim-go-webhooks.route_prefix', 'api/v1/providers/esim-go'))
    ->middleware(['api', 'throttle:'.config('providers-esim-go-webhooks.rate_limit', '30,1')])
    ->group(function () {
        Route::post(config('providers-esim-go-webhooks.callback_path', 'callback'), [EsimGoWebhookController::class, 'handle']);
    });
