<?php

namespace Vendor\FilamentAccountingIr\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ChartAccountResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'tenant_id' => $this->tenant_id,
            'company_id' => $this->company_id,
            'plan_id' => $this->plan_id,
            'type_id' => $this->type_id,
            'parent_id' => $this->parent_id,
            'code' => $this->code,
            'name' => $this->name,
            'level' => $this->level,
            'is_postable' => (bool) $this->is_postable,
            'is_active' => (bool) $this->is_active,
            'requires_dimensions' => $this->requires_dimensions ?? [],
            'created_at' => optional($this->created_at)->toISOString(),
            'updated_at' => optional($this->updated_at)->toISOString(),
        ];
    }
}
