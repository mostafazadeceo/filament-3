<?php

namespace Haida\FilamentMeetings\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MeetingMinuteResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->getKey(),
            'tenant_id' => $this->tenant_id,
            'meeting_id' => $this->meeting_id,
            'overview_text' => $this->overview_text,
            'keywords' => $this->keywords_json,
            'outline' => $this->outline_json,
            'summary_markdown' => $this->summary_markdown,
            'decisions' => $this->decisions_json,
            'risks' => $this->risks_json,
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
