<?php

namespace Vendor\FilamentPayrollAttendanceIr\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TimesheetResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'company_id' => $this->company_id,
            'branch_id' => $this->branch_id,
            'employee_id' => $this->employee_id,
            'period_start' => $this->period_start,
            'period_end' => $this->period_end,
            'period_type' => $this->period_type,
            'status' => $this->status,
            'worked_minutes' => $this->worked_minutes,
            'overtime_minutes' => $this->overtime_minutes,
            'night_minutes' => $this->night_minutes,
            'friday_minutes' => $this->friday_minutes,
            'holiday_minutes' => $this->holiday_minutes,
            'late_minutes' => $this->late_minutes,
            'early_leave_minutes' => $this->early_leave_minutes,
            'absence_minutes' => $this->absence_minutes,
            'approved_by' => $this->approved_by,
            'approved_at' => $this->approved_at,
            'metadata' => $this->metadata,
        ];
    }
}
