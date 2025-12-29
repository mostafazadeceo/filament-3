<?php

namespace Haida\FilamentWorkhub\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TransitionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->getKey(),
            'tenant_id' => $this->tenant_id,
            'workflow_id' => $this->workflow_id,
            'name' => $this->name,
            'from_status_id' => $this->from_status_id,
            'to_status_id' => $this->to_status_id,
            'is_active' => (bool) $this->is_active,
            'sort_order' => $this->sort_order,
            'validators' => $this->validators,
            'post_actions' => $this->post_actions,
        ];
    }
}
