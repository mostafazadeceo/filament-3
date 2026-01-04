<?php

namespace Vendor\FilamentPayrollAttendanceIr\Application\UseCases;

use Vendor\FilamentPayrollAttendanceIr\Domain\Enums\ExceptionSeverity;
use Vendor\FilamentPayrollAttendanceIr\Domain\Enums\ExceptionStatus;
use Vendor\FilamentPayrollAttendanceIr\Domain\Models\AttendanceException;

class RaiseException
{
    /**
     * @param  array<string, mixed>  $payload
     */
    public function execute(array $payload): AttendanceException
    {
        $existing = AttendanceException::query()
            ->when(isset($payload['time_event_id']), fn ($query) => $query->where('time_event_id', $payload['time_event_id']))
            ->when(isset($payload['attendance_record_id']), fn ($query) => $query->where('attendance_record_id', $payload['attendance_record_id']))
            ->when(isset($payload['timesheet_id']), fn ($query) => $query->where('timesheet_id', $payload['timesheet_id']))
            ->where('type', $payload['type'])
            ->whereIn('status', ['open', 'in_review'])
            ->first();

        if ($existing) {
            return $existing;
        }

        $assignedTo = $payload['assigned_to'] ?? null;
        if (! $assignedTo) {
            $assignedTo = app(\Vendor\FilamentPayrollAttendanceIr\Application\Services\ExceptionAssigneeResolver::class)
                ->resolve($payload['tenant_id'] ?? null);
        }

        return AttendanceException::query()->create([
            'company_id' => $payload['company_id'],
            'branch_id' => $payload['branch_id'] ?? null,
            'employee_id' => $payload['employee_id'] ?? null,
            'attendance_record_id' => $payload['attendance_record_id'] ?? null,
            'time_event_id' => $payload['time_event_id'] ?? null,
            'timesheet_id' => $payload['timesheet_id'] ?? null,
            'type' => $payload['type'],
            'severity' => $payload['severity'] ?? ExceptionSeverity::Low,
            'status' => ExceptionStatus::Open,
            'detected_at' => $payload['detected_at'] ?? now(),
            'assigned_to' => $assignedTo,
            'metadata' => $payload['metadata'] ?? null,
        ]);
    }
}
