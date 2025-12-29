<?php

namespace Vendor\FilamentPayrollAttendanceIr\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class LeaveTypeResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'company_id' => $this->company_id,
            'name' => $this->name,
            'code' => $this->code,
            'type' => $this->type,
            'default_days_per_year' => $this->default_days_per_year,
            'requires_approval' => $this->requires_approval,
            'requires_document' => $this->requires_document,
            'is_active' => $this->is_active,
        ];
    }
}
