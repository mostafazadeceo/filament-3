<?php

use Filamat\IamSuite\Http\Middleware\ApiAuth;
use Filamat\IamSuite\Http\Middleware\ApiKeyAuth;
use Filamat\IamSuite\Http\Middleware\ResolveTenant;
use Haida\FilamentMeetings\Http\Controllers\Api\V1\MeetingActionItemController;
use Haida\FilamentMeetings\Http\Controllers\Api\V1\MeetingAgendaItemController;
use Haida\FilamentMeetings\Http\Controllers\Api\V1\MeetingAiController;
use Haida\FilamentMeetings\Http\Controllers\Api\V1\MeetingAttendeeController;
use Haida\FilamentMeetings\Http\Controllers\Api\V1\MeetingConsentController;
use Haida\FilamentMeetings\Http\Controllers\Api\V1\MeetingController;
use Haida\FilamentMeetings\Http\Controllers\Api\V1\MeetingMinutesController;
use Haida\FilamentMeetings\Http\Controllers\Api\V1\MeetingTemplateController;
use Haida\FilamentMeetings\Http\Controllers\Api\V1\MeetingTranscriptController;
use Haida\FilamentMeetings\Http\Controllers\Api\V1\OpenApiController;
use Illuminate\Support\Facades\Route;

Route::prefix('api/v1/meetings')
    ->middleware([
        'api',
        ApiKeyAuth::class,
        ApiAuth::class,
        ResolveTenant::class,
        'throttle:'.config('filament-meetings.api.rate_limit', '60,1'),
    ])
    ->group(function () {
        Route::get('/', [MeetingController::class, 'index'])
            ->middleware('filamat-iam.scope:meetings.view');
        Route::post('/', [MeetingController::class, 'store'])
            ->middleware('filamat-iam.scope:meetings.manage');
        Route::get('{meeting}', [MeetingController::class, 'show'])
            ->middleware('filamat-iam.scope:meetings.view')
            ->whereNumber('meeting');
        Route::put('{meeting}', [MeetingController::class, 'update'])
            ->middleware('filamat-iam.scope:meetings.manage')
            ->whereNumber('meeting');
        Route::delete('{meeting}', [MeetingController::class, 'destroy'])
            ->middleware('filamat-iam.scope:meetings.manage')
            ->whereNumber('meeting');

        Route::apiResource('templates', MeetingTemplateController::class)
            ->only(['index', 'show'])
            ->middleware('filamat-iam.scope:meetings.view');
        Route::apiResource('templates', MeetingTemplateController::class)
            ->only(['store', 'update', 'destroy'])
            ->middleware('filamat-iam.scope:meetings.templates.manage');

        Route::get('{meeting}/attendees', [MeetingAttendeeController::class, 'index'])
            ->middleware('filamat-iam.scope:meetings.view')
            ->whereNumber('meeting');
        Route::post('{meeting}/attendees', [MeetingAttendeeController::class, 'store'])
            ->middleware('filamat-iam.scope:meetings.manage')
            ->whereNumber('meeting');
        Route::put('attendees/{attendee}', [MeetingAttendeeController::class, 'update'])
            ->middleware('filamat-iam.scope:meetings.manage');
        Route::delete('attendees/{attendee}', [MeetingAttendeeController::class, 'destroy'])
            ->middleware('filamat-iam.scope:meetings.manage');

        Route::get('{meeting}/agenda-items', [MeetingAgendaItemController::class, 'index'])
            ->middleware('filamat-iam.scope:meetings.view')
            ->whereNumber('meeting');
        Route::post('{meeting}/agenda-items', [MeetingAgendaItemController::class, 'store'])
            ->middleware('filamat-iam.scope:meetings.manage')
            ->whereNumber('meeting');
        Route::put('agenda-items/{agendaItem}', [MeetingAgendaItemController::class, 'update'])
            ->middleware('filamat-iam.scope:meetings.manage');
        Route::delete('agenda-items/{agendaItem}', [MeetingAgendaItemController::class, 'destroy'])
            ->middleware('filamat-iam.scope:meetings.manage');

        Route::post('{meeting}/consent/confirm', [MeetingConsentController::class, 'confirm'])
            ->middleware('filamat-iam.scope:meetings.ai.use')
            ->whereNumber('meeting');

        Route::post('{meeting}/transcript/upload', [MeetingTranscriptController::class, 'upload'])
            ->middleware('filamat-iam.scope:meetings.transcript.manage')
            ->whereNumber('meeting');
        Route::post('{meeting}/transcript/manual', [MeetingTranscriptController::class, 'manual'])
            ->middleware('filamat-iam.scope:meetings.transcript.manage')
            ->whereNumber('meeting');

        Route::post('{meeting}/ai/generate-agenda', [MeetingAiController::class, 'generateAgenda'])
            ->middleware('filamat-iam.scope:meetings.ai.use')
            ->whereNumber('meeting');
        Route::post('{meeting}/ai/generate-minutes', [MeetingAiController::class, 'generateMinutes'])
            ->middleware('filamat-iam.scope:meetings.ai.use')
            ->whereNumber('meeting');
        Route::post('{meeting}/ai/recap', [MeetingAiController::class, 'recap'])
            ->middleware('filamat-iam.scope:meetings.ai.use')
            ->whereNumber('meeting');

        Route::get('{meeting}/minutes/export', [MeetingMinutesController::class, 'export'])
            ->middleware('filamat-iam.scope:meetings.minutes.manage')
            ->whereNumber('meeting');

        Route::post('{meeting}/action-items/link-to-workhub', [MeetingActionItemController::class, 'linkToWorkhub'])
            ->middleware('filamat-iam.scope:meetings.action_items.manage')
            ->middleware('filamat-iam.scope:workhub.work_item.manage')
            ->whereNumber('meeting');

        Route::get('openapi', [OpenApiController::class, 'show'])
            ->middleware('filamat-iam.scope:meetings.view');
    });
