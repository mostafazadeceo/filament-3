<?php

namespace Vendor\FilamentPayrollAttendanceIr\Application\UseCases;

use Vendor\FilamentPayrollAttendanceIr\Domain\Enums\RequestStatus;
use Vendor\FilamentPayrollAttendanceIr\Domain\Models\LeaveRequest;

class RequestLeave
{
    /**
     * @param  array<string, mixed>  $payload
     */
    public function execute(array $payload): LeaveRequest
    {
        return LeaveRequest::query()->create([
            'company_id' => $payload['company_id'],
            'branch_id' => $payload['branch_id'] ?? null,
            'employee_id' => $payload['employee_id'],
            'leave_type_id' => $payload['leave_type_id'],
            'start_date' => $payload['start_date'],
            'end_date' => $payload['end_date'],
            'duration_hours' => $payload['duration_hours'] ?? 0,
            'status' => RequestStatus::Pending->value,
            'requested_by' => $payload['requested_by'] ?? auth()->id(),
            'notes' => $payload['notes'] ?? null,
        ]);
    }
}
