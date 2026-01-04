<?php

use Filamat\IamSuite\Http\Middleware\ApiAuth;
use Filamat\IamSuite\Http\Middleware\ApiKeyAuth;
use Filamat\IamSuite\Http\Middleware\ResolveTenant;
use Haida\MailtrapCore\Http\Controllers\Api\V1\AudienceContactController;
use Haida\MailtrapCore\Http\Controllers\Api\V1\AudienceController;
use Haida\MailtrapCore\Http\Controllers\Api\V1\CampaignController;
use Haida\MailtrapCore\Http\Controllers\Api\V1\ConnectionController;
use Haida\MailtrapCore\Http\Controllers\Api\V1\DomainController;
use Haida\MailtrapCore\Http\Controllers\Api\V1\InboxController;
use Haida\MailtrapCore\Http\Controllers\Api\V1\MessageController;
use Haida\MailtrapCore\Http\Controllers\Api\V1\OfferController;
use Haida\MailtrapCore\Http\Controllers\Api\V1\OpenApiController;
use Haida\MailtrapCore\Http\Controllers\Api\V1\SingleSendController;
use Illuminate\Support\Facades\Route;

Route::prefix('api/v1/mailtrap')
    ->middleware([
        'api',
        ApiKeyAuth::class,
        ApiAuth::class,
        ResolveTenant::class,
        'throttle:' . config('mailtrap-core.api.rate_limit', '60,1'),
    ])
    ->group(function () {
        Route::apiResource('connections', ConnectionController::class)
            ->middleware('filamat-iam.scope:mailtrap.connection.view')
            ->only(['index', 'show']);
        Route::apiResource('connections', ConnectionController::class)
            ->middleware('filamat-iam.scope:mailtrap.connection.manage')
            ->only(['store', 'update', 'destroy']);

        Route::get('inboxes', [InboxController::class, 'index'])
            ->middleware('filamat-iam.scope:mailtrap.inbox.view');
        Route::post('inboxes/sync', [InboxController::class, 'sync'])
            ->middleware('filamat-iam.scope:mailtrap.inbox.sync');
        Route::apiResource('inboxes', InboxController::class)
            ->middleware('filamat-iam.scope:mailtrap.inbox.manage')
            ->only(['store', 'update', 'destroy']);

        Route::get('messages', [MessageController::class, 'index'])
            ->middleware('filamat-iam.scope:mailtrap.message.view');
        Route::get('messages/{message}', [MessageController::class, 'show'])
            ->middleware('filamat-iam.scope:mailtrap.message.view');
        Route::get('messages/{message}/body', [MessageController::class, 'body'])
            ->middleware('filamat-iam.scope:mailtrap.message.view');
        Route::get('messages/{message}/attachments', [MessageController::class, 'attachments'])
            ->middleware('filamat-iam.scope:mailtrap.message.view');
        Route::get('messages/{message}/attachments/{attachment}', [MessageController::class, 'downloadAttachment'])
            ->middleware('filamat-iam.scope:mailtrap.message.view');

        Route::get('domains', [DomainController::class, 'index'])
            ->middleware('filamat-iam.scope:mailtrap.domain.view');
        Route::post('domains/sync', [DomainController::class, 'sync'])
            ->middleware('filamat-iam.scope:mailtrap.domain.sync');
        Route::apiResource('domains', DomainController::class)
            ->middleware('filamat-iam.scope:mailtrap.domain.manage')
            ->only(['store', 'update', 'destroy']);

        Route::apiResource('offers', OfferController::class)
            ->middleware('filamat-iam.scope:mailtrap.offer.view')
            ->only(['index', 'show']);
        Route::apiResource('offers', OfferController::class)
            ->middleware('filamat-iam.scope:mailtrap.offer.manage')
            ->only(['store', 'update', 'destroy']);

        Route::apiResource('audiences', AudienceController::class)
            ->middleware('filamat-iam.scope:mailtrap.audience.view')
            ->only(['index', 'show']);
        Route::apiResource('audiences', AudienceController::class)
            ->middleware('filamat-iam.scope:mailtrap.audience.manage')
            ->only(['store', 'update', 'destroy']);
        Route::get('audiences/{audience}/contacts', [AudienceContactController::class, 'index'])
            ->middleware('filamat-iam.scope:mailtrap.audience.view');
        Route::post('audiences/{audience}/contacts', [AudienceContactController::class, 'store'])
            ->middleware('filamat-iam.scope:mailtrap.audience.manage');
        Route::put('audiences/{audience}/contacts/{contact}', [AudienceContactController::class, 'update'])
            ->middleware('filamat-iam.scope:mailtrap.audience.manage');
        Route::delete('audiences/{audience}/contacts/{contact}', [AudienceContactController::class, 'destroy'])
            ->middleware('filamat-iam.scope:mailtrap.audience.manage');

        Route::apiResource('campaigns', CampaignController::class)
            ->middleware('filamat-iam.scope:mailtrap.campaign.view')
            ->only(['index', 'show']);
        Route::apiResource('campaigns', CampaignController::class)
            ->middleware('filamat-iam.scope:mailtrap.campaign.manage')
            ->only(['store', 'update', 'destroy']);
        Route::post('campaigns/{campaign}/send', [CampaignController::class, 'send'])
            ->middleware('filamat-iam.scope:mailtrap.campaign.send');

        Route::apiResource('single-sends', SingleSendController::class)
            ->middleware('filamat-iam.scope:mailtrap.send.single')
            ->only(['index', 'show', 'store']);

        Route::get('openapi', [OpenApiController::class, 'show'])
            ->middleware('filamat-iam.scope:mailtrap.connection.view');
    });
