<?php

use Haida\FilamentRelograde\Http\Controllers\RelogradeWebhookController;
use Illuminate\Support\Facades\Route;

Route::post('/relograde/webhook/order-finished', [RelogradeWebhookController::class, 'handle'])
    ->name('relograde.webhook.order-finished');
