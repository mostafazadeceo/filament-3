<?php

namespace Haida\FilamentMeetings\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MeetingTemplateResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->getKey(),
            'tenant_id' => $this->tenant_id,
            'name' => $this->name,
            'format' => $this->format,
            'scope' => $this->scope,
            'owner_id' => $this->owner_id,
            'sections_enabled' => $this->sections_enabled_json,
            'custom_prompts' => $this->custom_prompts_json,
            'minutes_schema' => $this->minutes_schema_json,
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
            'owner' => $this->whenLoaded('owner', fn () => [
                'id' => $this->owner?->getKey(),
                'name' => $this->owner?->name,
            ]),
        ];
    }
}
