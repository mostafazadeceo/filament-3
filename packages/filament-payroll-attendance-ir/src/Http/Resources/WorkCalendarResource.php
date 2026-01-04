<?php

namespace Vendor\FilamentPayrollAttendanceIr\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class WorkCalendarResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'company_id' => $this->company_id,
            'branch_id' => $this->branch_id,
            'name' => $this->name,
            'calendar_type' => $this->calendar_type,
            'timezone' => $this->timezone,
            'is_default' => (bool) $this->is_default,
            'metadata' => $this->metadata,
        ];
    }
}
