<?php

namespace Vendor\FilamentPayrollAttendanceIr\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PunchResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'company_id' => $this->company_id,
            'branch_id' => $this->branch_id,
            'employee_id' => $this->employee_id,
            'punch_at' => $this->punch_at,
            'type' => $this->type,
            'source' => $this->source,
            'device_ref' => $this->device_ref,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'created_at' => $this->created_at,
        ];
    }
}
