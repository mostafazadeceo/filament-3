<?php

namespace Vendor\FilamentPayrollAttendanceIr\Application\UseCases;

use Vendor\FilamentPayrollAttendanceIr\Models\PayrollAttendanceSchedule;

class AssignShift
{
    /**
     * @param  array<string, mixed>  $payload
     */
    public function execute(array $payload): PayrollAttendanceSchedule
    {
        return PayrollAttendanceSchedule::query()->updateOrCreate(
            [
                'employee_id' => $payload['employee_id'],
                'work_date' => $payload['work_date'],
            ],
            [
                'company_id' => $payload['company_id'],
                'branch_id' => $payload['branch_id'] ?? null,
                'shift_id' => $payload['shift_id'] ?? null,
                'status' => $payload['status'] ?? 'scheduled',
                'notes' => $payload['notes'] ?? null,
            ]
        );
    }
}
