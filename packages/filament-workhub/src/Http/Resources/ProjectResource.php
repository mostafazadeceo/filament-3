<?php

namespace Haida\FilamentWorkhub\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProjectResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->getKey(),
            'tenant_id' => $this->tenant_id,
            'workflow_id' => $this->workflow_id,
            'key' => $this->key,
            'name' => $this->name,
            'description' => $this->description,
            'status' => $this->status,
            'lead_user_id' => $this->lead_user_id,
            'start_date' => optional($this->start_date)->toDateString(),
            'due_date' => optional($this->due_date)->toDateString(),
            'allowed_link_types' => $this->allowed_link_types ?? [],
            'created_at' => optional($this->created_at)->toIso8601String(),
            'updated_at' => optional($this->updated_at)->toIso8601String(),
        ];
    }
}
