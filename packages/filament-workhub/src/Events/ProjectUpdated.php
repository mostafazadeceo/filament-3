<?php

namespace Haida\FilamentWorkhub\Events;

use Haida\FilamentWorkhub\Contracts\WorkhubEvent;
use Haida\FilamentWorkhub\DTOs\ProjectDto;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ProjectUpdated implements WorkhubEvent
{
    use Dispatchable;
    use SerializesModels;

    public function __construct(public ProjectDto $project, public array $changes = [])
    {
    }

    public function eventName(): string
    {
        return 'project.updated';
    }

    public function payload(): array
    {
        return [
            'event' => $this->eventName(),
            'project' => $this->project->toArray(),
            'changes' => $this->changes,
            'occurred_at' => now()->toIso8601String(),
        ];
    }

    public function tenantId(): ?int
    {
        return $this->project->tenantId;
    }
}
