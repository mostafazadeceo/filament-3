<?php

namespace Haida\FilamentWorkhub\Events;

use Haida\FilamentWorkhub\Contracts\WorkhubEvent;
use Haida\FilamentWorkhub\DTOs\WorkItemDto;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class WorkhubAiFieldGenerated implements WorkhubEvent
{
    use Dispatchable;
    use SerializesModels;

    /**
     * @param  array<string, mixed>  $payload
     */
    public function __construct(
        public WorkItemDto $workItem,
        public array $payload,
    ) {}

    public function eventName(): string
    {
        return 'workhub.ai.field.generated';
    }

    public function payload(): array
    {
        return [
            'event' => $this->eventName(),
            'work_item' => $this->workItem->toArray(),
            'payload' => $this->payload,
            'occurred_at' => now()->toIso8601String(),
        ];
    }

    public function tenantId(): ?int
    {
        return $this->workItem->tenantId;
    }
}
