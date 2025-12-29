<?php

namespace Haida\FilamentWorkhub\DTOs;

use Haida\FilamentWorkhub\Models\Project;

class ProjectData
{
    public function __construct(
        public int $id,
        public int $tenantId,
        public string $key,
        public string $name,
        public ?string $status,
    ) {}

    public static function fromModel(Project $project): self
    {
        return new self(
            $project->getKey(),
            (int) $project->tenant_id,
            (string) $project->key,
            (string) $project->name,
            $project->status,
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
        ];
    }
}
