<?php

namespace Vendor\FilamentPayrollAttendanceIr\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TimeEventResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'company_id' => $this->company_id,
            'branch_id' => $this->branch_id,
            'employee_id' => $this->employee_id,
            'event_at' => $this->event_at,
            'event_type' => $this->event_type,
            'source' => $this->source,
            'device_ref' => $this->device_ref,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'wifi_ssid' => $this->wifi_ssid,
            'ip_address' => $this->ip_address,
            'proof_type' => $this->proof_type,
            'proof_payload' => $this->proof_payload,
            'is_verified' => $this->is_verified,
            'verified_by' => $this->verified_by,
            'verified_at' => $this->verified_at,
            'metadata' => $this->metadata,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
