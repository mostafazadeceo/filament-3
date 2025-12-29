<?php

namespace Haida\FilamentWorkhub\Observers;

use Haida\FilamentWorkhub\DTOs\WorkItemDto;
use Haida\FilamentWorkhub\Events\WorkItemCreated;
use Haida\FilamentWorkhub\Events\WorkItemUpdated;
use Haida\FilamentWorkhub\Models\WorkItem;
use Haida\FilamentWorkhub\Services\WorkhubAuditService;

class WorkItemObserver
{
    public function created(WorkItem $workItem): void
    {
        app(WorkhubAuditService::class)->log('work_item.created', null, $workItem, [
            'key' => $workItem->key,
            'title' => $workItem->title,
        ]);

        event(new WorkItemCreated(WorkItemDto::fromModel($workItem)));
    }

    public function updated(WorkItem $workItem): void
    {
        $changes = $workItem->getChanges();
        unset($changes['updated_at']);

        if ($changes === []) {
            return;
        }

        if (array_key_exists('status_id', $changes)) {
            return;
        }

        app(WorkhubAuditService::class)->log('work_item.updated', null, $workItem, [
            'changes' => array_keys($changes),
        ]);

        event(new WorkItemUpdated(WorkItemDto::fromModel($workItem), array_keys($changes)));
    }
}
