<?php

namespace Haida\FilamentWorkhub\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WorkflowResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->getKey(),
            'tenant_id' => $this->tenant_id,
            'name' => $this->name,
            'description' => $this->description,
            'is_default' => (bool) $this->is_default,
            'created_at' => optional($this->created_at)->toIso8601String(),
            'updated_at' => optional($this->updated_at)->toIso8601String(),
        ];
    }
}
