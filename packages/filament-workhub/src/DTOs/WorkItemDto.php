<?php

namespace Haida\FilamentWorkhub\DTOs;

use Haida\FilamentWorkhub\Models\WorkItem;

final class WorkItemDto
{
    public function __construct(
        public int $id,
        public int $tenantId,
        public int $projectId,
        public string $key,
        public string $title,
        public ?int $statusId,
        public ?int $assigneeId,
        public ?string $priority,
        public ?string $dueDate,
    ) {}

    public static function fromModel(WorkItem $workItem): self
    {
        return new self(
            $workItem->getKey(),
            (int) $workItem->tenant_id,
            (int) $workItem->project_id,
            (string) $workItem->key,
            (string) $workItem->title,
            $workItem->status_id,
            $workItem->assignee_id,
            $workItem->priority,
            $workItem->due_date?->toDateString()
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'tenant_id' => $this->tenantId,
            'project_id' => $this->projectId,
            'key' => $this->key,
            'title' => $this->title,
            'status_id' => $this->statusId,
            'assignee_id' => $this->assigneeId,
            'priority' => $this->priority,
            'due_date' => $this->dueDate,
        ];
    }
}
