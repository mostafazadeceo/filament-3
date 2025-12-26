<?php

use Filamat\IamSuite\Http\Controllers\ImpersonationController;
use Filamat\IamSuite\Http\Controllers\Webhooks\NotificationWebhookController;
use Filamat\IamSuite\Http\Controllers\Webhooks\PaymentWebhookController;
use Illuminate\Support\Facades\Route;

Route::get('filamat-iam/impersonation/stop', [ImpersonationController::class, 'stop']);
Route::post('api/v1/webhooks/notification-plugin', NotificationWebhookController::class);
Route::post('api/v1/webhooks/payment-provider', PaymentWebhookController::class);
