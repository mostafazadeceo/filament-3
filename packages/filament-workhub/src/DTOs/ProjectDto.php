<?php

namespace Haida\FilamentWorkhub\DTOs;

use Haida\FilamentWorkhub\Models\Project;

final class ProjectDto
{
    public function __construct(
        public int $id,
        public int $tenantId,
        public string $key,
        public string $name,
        public ?string $status,
        public ?int $workflowId,
    ) {}

    public static function fromModel(Project $project): self
    {
        return new self(
            $project->getKey(),
            (int) $project->tenant_id,
            (string) $project->key,
            (string) $project->name,
            $project->status,
            $project->workflow_id
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
            'key' => $this->key,
            'name' => $this->name,
            'status' => $this->status,
            'workflow_id' => $this->workflowId,
        ];
    }
}
