<?php

namespace Haida\FilamentWorkhub\Events;

use Haida\FilamentWorkhub\Contracts\WorkhubEvent;
use Haida\FilamentWorkhub\DTOs\ProjectDto;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class WorkhubAiProjectReportCreated implements WorkhubEvent
{
    use Dispatchable;
    use SerializesModels;

    /**
     * @param  array<string, mixed>  $report
     */
    public function __construct(
        public ProjectDto $project,
        public array $report,
    ) {}

    public function eventName(): string
    {
        return 'workhub.ai.project_report.created';
    }

    public function payload(): array
    {
        return [
            'event' => $this->eventName(),
            'project' => $this->project->toArray(),
            'report' => $this->report,
            'occurred_at' => now()->toIso8601String(),
        ];
    }

    public function tenantId(): ?int
    {
        return $this->project->tenantId;
    }
}
