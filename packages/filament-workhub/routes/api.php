<?php

use Filamat\IamSuite\Http\Middleware\ApiAuth;
use Filamat\IamSuite\Http\Middleware\ApiKeyAuth;
use Filamat\IamSuite\Http\Middleware\ResolveTenant;
use Haida\FilamentWorkhub\Http\Controllers\Api\V1\AttachmentController;
use Haida\FilamentWorkhub\Http\Controllers\Api\V1\AutomationRuleController;
use Haida\FilamentWorkhub\Http\Controllers\Api\V1\CommentController;
use Haida\FilamentWorkhub\Http\Controllers\Api\V1\CustomFieldController;
use Haida\FilamentWorkhub\Http\Controllers\Api\V1\DecisionController;
use Haida\FilamentWorkhub\Http\Controllers\Api\V1\LabelController;
use Haida\FilamentWorkhub\Http\Controllers\Api\V1\LinkController;
use Haida\FilamentWorkhub\Http\Controllers\Api\V1\OpenApiController;
use Haida\FilamentWorkhub\Http\Controllers\Api\V1\ProjectAiController;
use Haida\FilamentWorkhub\Http\Controllers\Api\V1\ProjectController;
use Haida\FilamentWorkhub\Http\Controllers\Api\V1\StatusController;
use Haida\FilamentWorkhub\Http\Controllers\Api\V1\TimeEntryController;
use Haida\FilamentWorkhub\Http\Controllers\Api\V1\TransitionController;
use Haida\FilamentWorkhub\Http\Controllers\Api\V1\WatcherController;
use Haida\FilamentWorkhub\Http\Controllers\Api\V1\WorkflowController;
use Haida\FilamentWorkhub\Http\Controllers\Api\V1\WorkItemAiController;
use Haida\FilamentWorkhub\Http\Controllers\Api\V1\WorkItemController;
use Haida\FilamentWorkhub\Http\Controllers\Api\V1\WorkItemTransitionController;
use Haida\FilamentWorkhub\Http\Controllers\Api\V1\WorkTypeController;
use Illuminate\Support\Facades\Route;

Route::prefix('api/v1/workhub')
    ->middleware([
        'api',
        ApiKeyAuth::class,
        ApiAuth::class,
        ResolveTenant::class,
        'throttle:'.config('filament-workhub.api.rate_limit', '60,1'),
    ])
    ->group(function () {
        Route::apiResource('projects', ProjectController::class)
            ->only(['index', 'show'])
            ->middleware('filamat-iam.scope:workhub.project.view');
        Route::apiResource('projects', ProjectController::class)
            ->only(['store', 'update', 'destroy'])
            ->middleware('filamat-iam.scope:workhub.project.manage');

        Route::apiResource('work-items', WorkItemController::class)
            ->only(['index', 'show'])
            ->middleware('filamat-iam.scope:workhub.work_item.view');
        Route::apiResource('work-items', WorkItemController::class)
            ->only(['store', 'update', 'destroy'])
            ->middleware('filamat-iam.scope:workhub.work_item.manage');

        Route::apiResource('workflows', WorkflowController::class)
            ->only(['index', 'show'])
            ->middleware('filamat-iam.scope:workhub.workflow.view');
        Route::apiResource('workflows', WorkflowController::class)
            ->only(['store', 'update', 'destroy'])
            ->middleware('filamat-iam.scope:workhub.workflow.manage');

        Route::apiResource('statuses', StatusController::class)
            ->only(['index', 'show'])
            ->middleware('filamat-iam.scope:workhub.status.view');
        Route::apiResource('statuses', StatusController::class)
            ->only(['store', 'update', 'destroy'])
            ->middleware('filamat-iam.scope:workhub.status.manage');

        Route::apiResource('transitions', TransitionController::class)
            ->only(['index', 'show'])
            ->middleware('filamat-iam.scope:workhub.transition.view');
        Route::apiResource('transitions', TransitionController::class)
            ->only(['store', 'update', 'destroy'])
            ->middleware('filamat-iam.scope:workhub.transition.manage');

        Route::apiResource('work-types', WorkTypeController::class)
            ->only(['index', 'show'])
            ->middleware('filamat-iam.scope:workhub.work_type.view');
        Route::apiResource('work-types', WorkTypeController::class)
            ->only(['store', 'update', 'destroy'])
            ->middleware('filamat-iam.scope:workhub.work_type.manage');

        Route::apiResource('custom-fields', CustomFieldController::class)
            ->only(['index', 'show'])
            ->middleware('filamat-iam.scope:workhub.custom_field.view');
        Route::apiResource('custom-fields', CustomFieldController::class)
            ->only(['store', 'update', 'destroy'])
            ->middleware('filamat-iam.scope:workhub.custom_field.manage');

        Route::apiResource('automation-rules', AutomationRuleController::class)
            ->only(['index', 'show'])
            ->middleware('filamat-iam.scope:workhub.automation.view');
        Route::apiResource('automation-rules', AutomationRuleController::class)
            ->only(['store', 'update', 'destroy'])
            ->middleware('filamat-iam.scope:workhub.automation.manage');

        Route::post('work-items/{workItem}/transition', [WorkItemTransitionController::class, 'store'])
            ->middleware('filamat-iam.scope:workhub.transition.manage');

        Route::get('work-items/{workItem}/comments', [CommentController::class, 'index'])
            ->middleware('filamat-iam.scope:workhub.comment.view');
        Route::post('work-items/{workItem}/comments', [CommentController::class, 'store'])
            ->middleware('filamat-iam.scope:workhub.comment.manage');
        Route::delete('comments/{comment}', [CommentController::class, 'destroy'])
            ->middleware('filamat-iam.scope:workhub.comment.manage');

        Route::post('work-items/{workItem}/ai/personal-summary', [WorkItemAiController::class, 'personalSummary'])
            ->middleware('filamat-iam.scope:workhub.ai.use');
        Route::post('work-items/{workItem}/ai/shared-summary', [WorkItemAiController::class, 'sharedSummary'])
            ->middleware('filamat-iam.scope:workhub.ai.share');
        Route::post('work-items/{workItem}/ai/thread-summary', [WorkItemAiController::class, 'threadSummary'])
            ->middleware('filamat-iam.scope:workhub.ai.use');
        Route::post('work-items/{workItem}/ai/generate-subtasks', [WorkItemAiController::class, 'generateSubtasks'])
            ->middleware('filamat-iam.scope:workhub.ai.use');
        Route::post('work-items/{workItem}/ai/progress-update', [WorkItemAiController::class, 'progressUpdate'])
            ->middleware('filamat-iam.scope:workhub.ai.use');
        Route::post('work-items/{workItem}/ai/find-similar', [WorkItemAiController::class, 'findSimilar'])
            ->middleware('filamat-iam.scope:workhub.ai.use');

        Route::post('projects/{project}/ai/executive-summary', [ProjectAiController::class, 'executiveSummary'])
            ->middleware('filamat-iam.scope:workhub.ai.project_reports.manage');
        Route::get('projects/{project}/ai/stuck-tasks', [ProjectAiController::class, 'stuckTasks'])
            ->middleware('filamat-iam.scope:workhub.ai.project_reports.manage');

        Route::get('work-items/{workItem}/attachments', [AttachmentController::class, 'index'])
            ->middleware('filamat-iam.scope:workhub.attachment.view');
        Route::post('work-items/{workItem}/attachments', [AttachmentController::class, 'store'])
            ->middleware('filamat-iam.scope:workhub.attachment.manage');
        Route::delete('attachments/{attachment}', [AttachmentController::class, 'destroy'])
            ->middleware('filamat-iam.scope:workhub.attachment.manage');

        Route::get('work-items/{workItem}/watchers', [WatcherController::class, 'index'])
            ->middleware('filamat-iam.scope:workhub.watcher.view');
        Route::post('work-items/{workItem}/watchers', [WatcherController::class, 'store'])
            ->middleware('filamat-iam.scope:workhub.watcher.manage');
        Route::delete('watchers/{watcher}', [WatcherController::class, 'destroy'])
            ->middleware('filamat-iam.scope:workhub.watcher.manage');

        Route::apiResource('labels', LabelController::class)
            ->only(['index'])
            ->middleware('filamat-iam.scope:workhub.label.view');
        Route::apiResource('labels', LabelController::class)
            ->only(['store', 'update', 'destroy'])
            ->middleware('filamat-iam.scope:workhub.label.manage');

        Route::get('work-items/{workItem}/time-entries', [TimeEntryController::class, 'index'])
            ->middleware('filamat-iam.scope:workhub.time_entry.view');
        Route::post('work-items/{workItem}/time-entries', [TimeEntryController::class, 'store'])
            ->middleware('filamat-iam.scope:workhub.time_entry.manage');
        Route::delete('time-entries/{timeEntry}', [TimeEntryController::class, 'destroy'])
            ->middleware('filamat-iam.scope:workhub.time_entry.manage');

        Route::get('work-items/{workItem}/decisions', [DecisionController::class, 'index'])
            ->middleware('filamat-iam.scope:workhub.decision.view');
        Route::post('work-items/{workItem}/decisions', [DecisionController::class, 'store'])
            ->middleware('filamat-iam.scope:workhub.decision.manage');
        Route::delete('decisions/{decision}', [DecisionController::class, 'destroy'])
            ->middleware('filamat-iam.scope:workhub.decision.manage');

        Route::get('work-items/{workItem}/links', [LinkController::class, 'index'])
            ->middleware('filamat-iam.scope:workhub.link.view');
        Route::post('work-items/{workItem}/links', [LinkController::class, 'store'])
            ->middleware('filamat-iam.scope:workhub.link.manage');
        Route::delete('links/{link}', [LinkController::class, 'destroy'])
            ->middleware('filamat-iam.scope:workhub.link.manage');

        Route::get('openapi', [OpenApiController::class, 'show'])
            ->middleware('filamat-iam.scope:workhub.project.view');
    });
