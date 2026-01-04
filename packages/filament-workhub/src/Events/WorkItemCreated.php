<?php

namespace Haida\FilamentWorkhub\Events;

use Haida\FilamentWorkhub\Contracts\WorkhubEvent;
use Haida\FilamentWorkhub\DTOs\WorkItemDto;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class WorkItemCreated implements WorkhubEvent
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(public WorkItemDto $workItem, public array $meta = []) {}

    public function eventName(): string
    {
        return 'work_item.created';
    }

    public function payload(): array
    {
        return [
            'event' => $this->eventName(),
            'work_item' => $this->workItem->toArray(),
            'meta' => $this->meta,
            'occurred_at' => now()->toIso8601String(),
        ];
    }

    public function tenantId(): ?int
    {
        return $this->workItem->tenantId;
    }
}
