<?php

namespace Haida\FilamentWorkhub\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StatusResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->getKey(),
            'tenant_id' => $this->tenant_id,
            'workflow_id' => $this->workflow_id,
            'name' => $this->name,
            'slug' => $this->slug,
            'category' => $this->category,
            'color' => $this->color,
            'sort_order' => $this->sort_order,
            'is_default' => (bool) $this->is_default,
        ];
    }
}
