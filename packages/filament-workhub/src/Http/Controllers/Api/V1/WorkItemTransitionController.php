<?php

namespace Haida\FilamentWorkhub\Http\Controllers\Api\V1;

use Haida\FilamentWorkhub\Http\Requests\WorkItemTransitionRequest;
use Haida\FilamentWorkhub\Http\Resources\WorkItemResource;
use Haida\FilamentWorkhub\Models\Status;
use Haida\FilamentWorkhub\Models\WorkItem;
use Haida\FilamentWorkhub\Services\WorkflowTransitionService;

class WorkItemTransitionController extends ApiController
{
    public function store(WorkItemTransitionRequest $request, WorkItem $workItem): WorkItemResource
    {
        $this->authorize('update', $workItem);

        $statusId = (int) $request->validated()['to_status_id'];
        $status = Status::query()->findOrFail($statusId);

        $updated = app(WorkflowTransitionService::class)->transition($workItem, $status);

        return new WorkItemResource($updated->refresh()->load('labels'));
    }
}
