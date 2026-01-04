<?php

namespace Haida\FilamentWorkhub\Events;

use Haida\FilamentWorkhub\Contracts\WorkhubEvent;
use Haida\FilamentWorkhub\DTOs\ProjectDto;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ProjectCreated implements WorkhubEvent
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(public ProjectDto $project, public array $meta = []) {}

    public function eventName(): string
    {
        return 'project.created';
    }

    public function payload(): array
    {
        return [
            'event' => $this->eventName(),
            'project' => $this->project->toArray(),
            'meta' => $this->meta,
            'occurred_at' => now()->toIso8601String(),
        ];
    }

    public function tenantId(): ?int
    {
        return $this->project->tenantId;
    }
}
