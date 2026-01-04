<?php

use Filamat\IamSuite\Http\Middleware\ApiAuth;
use Filamat\IamSuite\Http\Middleware\ApiKeyAuth;
use Filamat\IamSuite\Http\Middleware\ResolveTenant;
use Haida\FilamentMailOps\Http\Controllers\Api\V1\AliasController;
use Haida\FilamentMailOps\Http\Controllers\Api\V1\DomainController;
use Haida\FilamentMailOps\Http\Controllers\Api\V1\InboundMessageController;
use Haida\FilamentMailOps\Http\Controllers\Api\V1\MailboxController;
use Haida\FilamentMailOps\Http\Controllers\Api\V1\OpenApiController;
use Haida\FilamentMailOps\Http\Controllers\Api\V1\OutboundMessageController;
use Illuminate\Support\Facades\Route;

Route::prefix('api/v1/filament-mailops')
    ->middleware([
        'api',
        ApiKeyAuth::class,
        ApiAuth::class,
        ResolveTenant::class,
        'throttle:'.config('filament-mailops.api.rate_limit', '60,1'),
    ])
    ->group(function () {
        Route::get('openapi', [OpenApiController::class, 'show'])
            ->middleware('filamat-iam.scope:mailops.domain.view');

        Route::apiResource('domains', DomainController::class)
            ->middleware('filamat-iam.scope:mailops.domain');

        Route::apiResource('mailboxes', MailboxController::class)
            ->middleware('filamat-iam.scope:mailops.mailbox');

        Route::apiResource('aliases', AliasController::class)
            ->middleware('filamat-iam.scope:mailops.alias');

        Route::get('outbound-messages', [OutboundMessageController::class, 'index'])
            ->middleware('filamat-iam.scope:mailops.outbound.view');

        Route::get('outbound-messages/{message}', [OutboundMessageController::class, 'show'])
            ->middleware('filamat-iam.scope:mailops.outbound.view');

        Route::post('outbound-messages', [OutboundMessageController::class, 'store'])
            ->middleware('filamat-iam.scope:mailops.outbound.send');

        Route::get('inbound-messages', [InboundMessageController::class, 'index'])
            ->middleware('filamat-iam.scope:mailops.inbound.view');

        Route::get('inbound-messages/{message}', [InboundMessageController::class, 'show'])
            ->middleware('filamat-iam.scope:mailops.inbound.view');

        Route::post('inbound-messages/sync', [InboundMessageController::class, 'sync'])
            ->middleware('filamat-iam.scope:mailops.inbound.sync');
    });
