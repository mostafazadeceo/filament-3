<?php

declare(strict_types=1);

use Filamat\IamSuite\Http\Middleware\ApiAuth;
use Filamat\IamSuite\Http\Middleware\ApiKeyAuth;
use Filamat\IamSuite\Http\Middleware\ResolveTenant;
use Haida\SmsBulk\Http\Controllers\Api\V1\CampaignActionController;
use Haida\SmsBulk\Http\Controllers\Api\V1\CampaignController;
use Haida\SmsBulk\Http\Controllers\Api\V1\ContactController;
use Haida\SmsBulk\Http\Controllers\Api\V1\CreditController;
use Haida\SmsBulk\Http\Controllers\Api\V1\DraftController;
use Haida\SmsBulk\Http\Controllers\Api\V1\ImportController;
use Haida\SmsBulk\Http\Controllers\Api\V1\OpenApiController;
use Haida\SmsBulk\Http\Controllers\Api\V1\OptInOutController;
use Haida\SmsBulk\Http\Controllers\Api\V1\PatternController;
use Haida\SmsBulk\Http\Controllers\Api\V1\PhonebookController;
use Haida\SmsBulk\Http\Controllers\Api\V1\PhonebookOptionController;
use Haida\SmsBulk\Http\Controllers\Api\V1\ProviderConnectionController;
use Haida\SmsBulk\Http\Controllers\Api\V1\ReportController;
use Illuminate\Support\Facades\Route;

Route::prefix(config('filament-sms-bulk.api.prefix', 'api/v1/sms-bulk'))
    ->middleware([
        'api',
        ApiKeyAuth::class,
        ApiAuth::class,
        ResolveTenant::class,
        'throttle:'.config('filament-sms-bulk.api.rate_limit', '60,1'),
    ])
    ->group(function (): void {
        Route::get('credit', CreditController::class)
            ->middleware('filamat-iam.scope:sms-bulk.view');

        Route::apiResource('provider-connections', ProviderConnectionController::class)
            ->middleware('filamat-iam.scope:sms-bulk.connection.manage');

        Route::apiResource('phonebooks', PhonebookController::class)
            ->middleware('filamat-iam.scope:sms-bulk.phonebook.manage');

        Route::get('phonebooks/options', [PhonebookOptionController::class, 'index'])
            ->middleware('filamat-iam.scope:sms-bulk.phonebook.view');
        Route::post('phonebooks/options', [PhonebookOptionController::class, 'store'])
            ->middleware('filamat-iam.scope:sms-bulk.phonebook.manage');
        Route::put('phonebooks/options/{id}', [PhonebookOptionController::class, 'update'])
            ->middleware('filamat-iam.scope:sms-bulk.phonebook.manage');
        Route::delete('phonebooks/options/{id}', [PhonebookOptionController::class, 'destroy'])
            ->middleware('filamat-iam.scope:sms-bulk.phonebook.manage');

        Route::get('phonebooks/contacts', [ContactController::class, 'index'])
            ->middleware('filamat-iam.scope:sms-bulk.phonebook.view');
        Route::post('phonebooks/contacts', [ContactController::class, 'store'])
            ->middleware('filamat-iam.scope:sms-bulk.phonebook.manage');
        Route::put('phonebooks/contacts/{id}', [ContactController::class, 'update'])
            ->middleware('filamat-iam.scope:sms-bulk.phonebook.manage');
        Route::delete('phonebooks/contacts/{id}', [ContactController::class, 'destroy'])
            ->middleware('filamat-iam.scope:sms-bulk.phonebook.manage');

        Route::post('imports/contacts', [ImportController::class, 'store'])
            ->middleware('filamat-iam.scope:sms-bulk.phonebook.import');
        Route::get('imports/{id}', [ImportController::class, 'show'])
            ->middleware('filamat-iam.scope:sms-bulk.phonebook.view');

        Route::apiResource('patterns', PatternController::class)
            ->middleware('filamat-iam.scope:sms-bulk.pattern.manage');

        Route::apiResource('drafts', DraftController::class)
            ->middleware('filamat-iam.scope:sms-bulk.draft.manage');

        Route::get('campaigns', [CampaignController::class, 'index'])
            ->middleware('filamat-iam.scope:sms-bulk.campaign.view');
        Route::post('campaigns', [CampaignController::class, 'store'])
            ->middleware('filamat-iam.scope:sms-bulk.campaign.manage');
        Route::get('campaigns/{id}', [CampaignController::class, 'show'])
            ->middleware('filamat-iam.scope:sms-bulk.campaign.view');

        Route::post('campaigns/{id}/submit', [CampaignActionController::class, 'submit'])
            ->middleware('filamat-iam.scope:sms-bulk.campaign.submit');
        Route::post('campaigns/{id}/pause', [CampaignActionController::class, 'pause'])
            ->middleware('filamat-iam.scope:sms-bulk.campaign.pause');
        Route::post('campaigns/{id}/resume', [CampaignActionController::class, 'resume'])
            ->middleware('filamat-iam.scope:sms-bulk.campaign.resume');
        Route::post('campaigns/{id}/cancel', [CampaignActionController::class, 'cancel'])
            ->middleware('filamat-iam.scope:sms-bulk.campaign.cancel');

        Route::get('reports/outbox', [ReportController::class, 'outbox'])
            ->middleware('filamat-iam.scope:sms-bulk.report.view');
        Route::get('reports/inbox', [ReportController::class, 'inbox'])
            ->middleware('filamat-iam.scope:sms-bulk.report.view');
        Route::get('reports/bulk/{campaignId}/recipients', [ReportController::class, 'bulkRecipients'])
            ->middleware('filamat-iam.scope:sms-bulk.report.view');
        Route::get('reports/export/csv', [ReportController::class, 'exportCsv'])
            ->name('sms-bulk.reports.export.csv')
            ->middleware('filamat-iam.scope:sms-bulk.report.export');

        Route::post('optout', [OptInOutController::class, 'optOut'])
            ->middleware('filamat-iam.scope:sms-bulk.suppression.manage');
        Route::post('optin', [OptInOutController::class, 'optIn'])
            ->middleware('filamat-iam.scope:sms-bulk.suppression.manage');

        Route::get('openapi', OpenApiController::class)
            ->middleware('filamat-iam.scope:sms-bulk.view');
    });
