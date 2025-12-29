<?php

namespace Haida\FilamentWorkhub\Services;

use Haida\FilamentWorkhub\Models\Status;
use Haida\FilamentWorkhub\Models\WorkItem;

class WorkItemTransitionService
{
    public function transition(WorkItem $workItem, int $toStatusId): WorkItem
    {
        $status = Status::query()->findOrFail($toStatusId);

        return app(WorkflowTransitionService::class)->transition($workItem, $status);
    }
}
