<?php

namespace Haida\FilamentWorkhub\DTOs;

use Haida\FilamentWorkhub\Models\WorkItem;

class TransitionData
{
    public function __construct(
        public int $workItemId,
        public int $tenantId,
        public int $projectId,
        public int $fromStatusId,
        public int $toStatusId,
    ) {}

    public static function fromWorkItem(WorkItem $workItem, int $fromStatusId, int $toStatusId): self
    {
        return new self(
            $workItem->getKey(),
            (int) $workItem->tenant_id,
            (int) $workItem->project_id,
            $fromStatusId,
            $toStatusId,
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'work_item_id' => $this->workItemId,
            'tenant_id' => $this->tenantId,
            'project_id' => $this->projectId,
            'from_status_id' => $this->fromStatusId,
            'to_status_id' => $this->toStatusId,
        ];
    }
}
