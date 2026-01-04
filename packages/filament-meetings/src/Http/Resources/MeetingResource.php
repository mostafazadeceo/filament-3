<?php

namespace Haida\FilamentMeetings\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MeetingResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->getKey(),
            'tenant_id' => $this->tenant_id,
            'title' => $this->title,
            'scheduled_at' => $this->scheduled_at?->toIso8601String(),
            'duration_minutes' => $this->duration_minutes,
            'location_type' => $this->location_type,
            'location_value' => $this->location_value,
            'organizer_id' => $this->organizer_id,
            'status' => $this->status,
            'ai_enabled' => (bool) $this->ai_enabled,
            'consent_required' => (bool) $this->consent_required,
            'consent_mode' => $this->consent_mode,
            'consent_confirmed_at' => $this->consent_confirmed_at?->toIso8601String(),
            'share_minutes_mode' => $this->share_minutes_mode,
            'minutes_format' => $this->minutes_format,
            'meta' => $this->meta,
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
            'organizer' => $this->whenLoaded('organizer', fn () => [
                'id' => $this->organizer?->getKey(),
                'name' => $this->organizer?->name,
            ]),
            'attendees' => MeetingAttendeeResource::collection($this->whenLoaded('attendees')),
            'agenda_items' => MeetingAgendaItemResource::collection($this->whenLoaded('agendaItems')),
            'notes' => $this->whenLoaded('notes', fn () => [
                'content' => $this->notes?->content_longtext,
                'updated_by' => $this->notes?->updated_by,
            ]),
            'minutes' => MeetingMinuteResource::collection($this->whenLoaded('minutes')),
            'action_items' => MeetingActionItemResource::collection($this->whenLoaded('actionItems')),
        ];
    }
}
