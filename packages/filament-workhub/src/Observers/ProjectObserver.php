<?php

namespace Haida\FilamentWorkhub\Observers;

use Haida\FilamentWorkhub\DTOs\ProjectDto;
use Haida\FilamentWorkhub\Events\ProjectCreated;
use Haida\FilamentWorkhub\Events\ProjectUpdated;
use Haida\FilamentWorkhub\Models\Project;
use Haida\FilamentWorkhub\Services\WorkhubAuditService;

class ProjectObserver
{
    public function created(Project $project): void
    {
        app(WorkhubAuditService::class)->log('project.created', $project, null, [
            'name' => $project->name,
            'key' => $project->key,
        ]);

        event(new ProjectCreated(ProjectDto::fromModel($project)));
    }

    public function updated(Project $project): void
    {
        $changes = $project->getChanges();
        unset($changes['updated_at']);

        if ($changes === []) {
            return;
        }

        app(WorkhubAuditService::class)->log('project.updated', $project, null, [
            'changes' => array_keys($changes),
        ]);

        event(new ProjectUpdated(ProjectDto::fromModel($project), array_keys($changes)));
    }
}
