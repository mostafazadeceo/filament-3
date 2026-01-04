<?php

namespace Haida\FilamentMeetings\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MeetingAttendeeResource extends JsonResource
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
            'user_id' => $this->user_id,
            'name' => $this->name,
            'email_masked' => $this->email_masked,
            'role' => $this->role,
            'invited_at' => $this->invited_at?->toIso8601String(),
            'responded_at' => $this->responded_at?->toIso8601String(),
            'attendance_status' => $this->attendance_status,
            'consent_granted_at' => $this->consent_granted_at?->toIso8601String(),
        ];
    }
}
