<?php

namespace Haida\FilamentWorkhub\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AutomationRuleResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->getKey(),
            'tenant_id' => $this->tenant_id,
            'project_id' => $this->project_id,
            'name' => $this->name,
            'is_active' => (bool) $this->is_active,
            'trigger_type' => $this->trigger_type,
            'trigger_config' => $this->trigger_config,
            'conditions' => $this->conditions,
            'actions' => $this->actions,
            'last_ran_at' => optional($this->last_ran_at)->toIso8601String(),
            'created_at' => optional($this->created_at)->toIso8601String(),
            'updated_at' => optional($this->updated_at)->toIso8601String(),
        ];
    }
}
