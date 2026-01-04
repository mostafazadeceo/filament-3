<?php

namespace Haida\FilamentMeetings\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MeetingAgendaItemResource extends JsonResource
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
            'sort_order' => $this->sort_order,
            'title' => $this->title,
            'description' => $this->description,
            'owner_id' => $this->owner_id,
            'timebox_minutes' => $this->timebox_minutes,
        ];
    }
}
