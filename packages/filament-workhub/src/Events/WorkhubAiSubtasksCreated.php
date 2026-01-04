<?php

namespace Haida\FilamentWorkhub\Events;

use Haida\FilamentWorkhub\Contracts\WorkhubEvent;
use Haida\FilamentWorkhub\DTOs\WorkItemDto;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class WorkhubAiSubtasksCreated implements WorkhubEvent
{
    use Dispatchable;
    use SerializesModels;

    /**
     * @param  array<int, array<string, mixed>>  $subtasks
     */
    public function __construct(
        public WorkItemDto $workItem,
        public array $subtasks,
    ) {}

    public function eventName(): string
    {
        return 'workhub.ai.subtasks.created';
    }

    public function payload(): array
    {
        return [
            'event' => $this->eventName(),
            'work_item' => $this->workItem->toArray(),
            'subtasks' => $this->subtasks,
            'occurred_at' => now()->toIso8601String(),
        ];
    }

    public function tenantId(): ?int
    {
        return $this->workItem->tenantId;
    }
}
