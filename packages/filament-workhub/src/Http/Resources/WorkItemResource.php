<?php

namespace Haida\FilamentWorkhub\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WorkItemResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->getKey(),
            'tenant_id' => $this->tenant_id,
            'project_id' => $this->project_id,
            'work_type_id' => $this->work_type_id,
            'workflow_id' => $this->workflow_id,
            'status_id' => $this->status_id,
            'number' => $this->number,
            'key' => $this->key,
            'title' => $this->title,
            'description' => $this->description,
            'priority' => $this->priority,
            'reporter_id' => $this->reporter_id,
            'assignee_id' => $this->assignee_id,
            'due_date' => optional($this->due_date)->toDateString(),
            'started_at' => optional($this->started_at)->toIso8601String(),
            'completed_at' => optional($this->completed_at)->toIso8601String(),
            'estimate_minutes' => $this->estimate_minutes,
            'sort_order' => $this->sort_order,
            'labels' => $this->whenLoaded('labels', fn () => $this->labels->map(fn ($label) => [
                'id' => $label->getKey(),
                'name' => $label->name,
                'slug' => $label->slug,
                'color' => $label->color,
            ])),
            'custom_fields' => $this->whenLoaded('customFieldValues', function () {
                return $this->customFieldValues
                    ->mapWithKeys(fn ($value) => [$value->field?->key => $value->value])
                    ->filter(fn ($value, $key) => $key !== null)
                    ->toArray();
            }),
            'created_at' => optional($this->created_at)->toIso8601String(),
            'updated_at' => optional($this->updated_at)->toIso8601String(),
        ];
    }
}
