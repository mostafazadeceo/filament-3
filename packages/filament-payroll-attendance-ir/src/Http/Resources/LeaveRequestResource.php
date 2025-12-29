<?php

namespace Vendor\FilamentPayrollAttendanceIr\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class LeaveRequestResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'company_id' => $this->company_id,
            'branch_id' => $this->branch_id,
            'employee_id' => $this->employee_id,
            'leave_type_id' => $this->leave_type_id,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'duration_hours' => $this->duration_hours,
            'status' => $this->status,
            'approved_at' => $this->approved_at,
            'notes' => $this->notes,
        ];
    }
}
