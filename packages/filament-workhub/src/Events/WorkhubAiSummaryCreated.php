<?php

namespace Haida\FilamentWorkhub\Events;

use Haida\FilamentWorkhub\Contracts\WorkhubEvent;
use Haida\FilamentWorkhub\DTOs\WorkItemDto;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class WorkhubAiSummaryCreated implements WorkhubEvent
{
    use Dispatchable;
    use SerializesModels;

    /**
     * @param  array<string, mixed>  $summary
     */
    public function __construct(
        public WorkItemDto $workItem,
        public array $summary,
        public string $type,
        public ?int $summaryId = null,
    ) {}

    public function eventName(): string
    {
        return 'workhub.ai.summary.created';
    }

    public function payload(): array
    {
        return [
            'event' => $this->eventName(),
            'work_item' => $this->workItem->toArray(),
            'summary_id' => $this->summaryId,
            'type' => $this->type,
            'summary' => $this->summary,
            'occurred_at' => now()->toIso8601String(),
        ];
    }

    public function tenantId(): ?int
    {
        return $this->workItem->tenantId;
    }
}
