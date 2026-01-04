<?php

declare(strict_types=1);

use Filamat\IamSuite\Http\Middleware\ApiAuth;
use Filamat\IamSuite\Http\Middleware\ApiKeyAuth;
use Filamat\IamSuite\Http\Middleware\ResolveTenant;
use Haida\FilamentAppApi\Http\Controllers\Api\V1\AppConfigController;
use Haida\FilamentAppApi\Http\Controllers\Api\V1\AuthController;
use Haida\FilamentAppApi\Http\Controllers\Api\V1\CapabilityController;
use Haida\FilamentAppApi\Http\Controllers\Api\V1\DeviceController;
use Haida\FilamentAppApi\Http\Controllers\Api\V1\NotificationFeedController;
use Haida\FilamentAppApi\Http\Controllers\Api\V1\OpenApiController;
use Haida\FilamentAppApi\Http\Controllers\Api\V1\RealtimeSignalController;
use Haida\FilamentAppApi\Http\Controllers\Api\V1\SupportAttachmentController;
use Haida\FilamentAppApi\Http\Controllers\Api\V1\SupportMessageController;
use Haida\FilamentAppApi\Http\Controllers\Api\V1\SupportTicketController;
use Haida\FilamentAppApi\Http\Controllers\Api\V1\SyncController;
use Haida\FilamentAppApi\Http\Controllers\Api\V1\TenantController;
use Illuminate\Support\Facades\Route;

Route::prefix('api/v1/app')
    ->middleware([
        'api',
        ApiKeyAuth::class,
        ApiAuth::class,
        ResolveTenant::class,
        'throttle:'.config('filament-app-api.api.rate_limit', '60,1'),
    ])
    ->group(function () {
        Route::post('auth/login', [AuthController::class, 'login']);
        Route::post('auth/refresh', [AuthController::class, 'refresh']);
        Route::post('auth/logout', [AuthController::class, 'logout'])->middleware('filamat-iam.scope:app.view');
        Route::get('auth/me', [AuthController::class, 'me'])->middleware('filamat-iam.scope:app.view');

        Route::get('tenant/current', [TenantController::class, 'current'])->middleware('filamat-iam.scope:app.tenant.view');
        Route::post('tenant/switch', [TenantController::class, 'switch'])->middleware('filamat-iam.scope:app.tenant.switch');

        Route::get('capabilities', [CapabilityController::class, 'index'])->middleware('filamat-iam.scope:app.view');
        Route::get('config', [AppConfigController::class, 'show'])->middleware('filamat-iam.scope:app.config.view');

        Route::post('sync/push', [SyncController::class, 'push'])->middleware('filamat-iam.scope:app.sync');
        Route::get('sync/pull', [SyncController::class, 'pull'])->middleware('filamat-iam.scope:app.sync');
        Route::post('sync/conflicts', [SyncController::class, 'conflicts'])->middleware('filamat-iam.scope:app.sync');

        Route::post('devices', [DeviceController::class, 'store'])->middleware('filamat-iam.scope:app.device.manage');
        Route::post('devices/{device}/tokens', [DeviceController::class, 'storeToken'])->middleware('filamat-iam.scope:app.device.manage');
        Route::delete('devices/{device}', [DeviceController::class, 'destroy'])->middleware('filamat-iam.scope:app.device.manage');

        Route::get('notifications', [NotificationFeedController::class, 'index'])->middleware('filamat-iam.scope:app.notification.view');
        Route::post('notifications/{notification}/read', [NotificationFeedController::class, 'markRead'])->middleware('filamat-iam.scope:app.notification.manage');

        Route::get('tickets', [SupportTicketController::class, 'index'])->middleware('filamat-iam.scope:support.ticket.view');
        Route::post('tickets', [SupportTicketController::class, 'store'])->middleware('filamat-iam.scope:support.ticket.manage');
        Route::get('tickets/{ticket}/messages', [SupportMessageController::class, 'index'])->middleware('filamat-iam.scope:support.message.view');
        Route::post('tickets/{ticket}/messages', [SupportMessageController::class, 'store'])->middleware('filamat-iam.scope:support.message.manage');
        Route::post('tickets/{ticket}/attachments', [SupportAttachmentController::class, 'store'])->middleware('filamat-iam.scope:support.attachment.manage');

        Route::get('realtime/signals', [RealtimeSignalController::class, 'index'])->middleware('filamat-iam.scope:app.realtime.signal');
        Route::post('realtime/signals', [RealtimeSignalController::class, 'store'])->middleware('filamat-iam.scope:app.realtime.signal');

        Route::get('openapi', [OpenApiController::class, 'show'])->middleware('filamat-iam.scope:app.view');
    });
