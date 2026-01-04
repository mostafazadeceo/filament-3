<?php

namespace Vendor\FilamentPayrollAttendanceIr\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AttendanceExceptionResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'company_id' => $this->company_id,
            'branch_id' => $this->branch_id,
            'employee_id' => $this->employee_id,
            'attendance_record_id' => $this->attendance_record_id,
            'time_event_id' => $this->time_event_id,
            'timesheet_id' => $this->timesheet_id,
            'type' => $this->type,
            'severity' => $this->severity,
            'status' => $this->status,
            'detected_at' => $this->detected_at,
            'assigned_to' => $this->assigned_to,
            'resolved_by' => $this->resolved_by,
            'resolved_at' => $this->resolved_at,
            'resolution_notes' => $this->resolution_notes,
            'metadata' => $this->metadata,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
