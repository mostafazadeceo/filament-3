<?php

namespace Vendor\FilamentPayrollAttendanceIr\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AttendanceRecordResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'company_id' => $this->company_id,
            'branch_id' => $this->branch_id,
            'employee_id' => $this->employee_id,
            'shift_id' => $this->shift_id,
            'work_date' => $this->work_date,
            'scheduled_in' => $this->scheduled_in,
            'scheduled_out' => $this->scheduled_out,
            'actual_in' => $this->actual_in,
            'actual_out' => $this->actual_out,
            'worked_minutes' => $this->worked_minutes,
            'overtime_minutes' => $this->overtime_minutes,
            'night_minutes' => $this->night_minutes,
            'friday_minutes' => $this->friday_minutes,
            'holiday_minutes' => $this->holiday_minutes,
            'late_minutes' => $this->late_minutes,
            'early_leave_minutes' => $this->early_leave_minutes,
            'absence_minutes' => $this->absence_minutes,
            'status' => $this->status,
        ];
    }
}
