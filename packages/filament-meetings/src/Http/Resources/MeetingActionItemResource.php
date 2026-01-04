<?php

namespace Haida\FilamentMeetings\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MeetingActionItemResource extends JsonResource
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
            'title' => $this->title,
            'description' => $this->description,
            'assignee_id' => $this->assignee_id,
            'due_date' => $this->due_date?->toDateString(),
            'priority' => $this->priority,
            'status' => $this->status,
            'linked_workhub_item_id' => $this->linked_workhub_item_id,
        ];
    }
}
