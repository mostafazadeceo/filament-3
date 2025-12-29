<?php

namespace Vendor\FilamentPayrollAttendanceIr\Services;

use Carbon\Carbon;
use Vendor\FilamentPayrollAttendanceIr\Models\PayrollAttendanceRecord;

class AttendanceSummaryService
{
    /**
     * @return array<string, int>
     */
    public function summarize(int $employeeId, Carbon $start, Carbon $end): array
    {
        $records = PayrollAttendanceRecord::query()
            ->where('employee_id', $employeeId)
            ->whereBetween('work_date', [$start->toDateString(), $end->toDateString()])
            ->whereIn('status', ['approved', 'locked'])
            ->get();

        $fields = [
            'worked_minutes',
            'overtime_minutes',
            'night_minutes',
            'friday_minutes',
            'holiday_minutes',
            'late_minutes',
            'early_leave_minutes',
            'absence_minutes',
        ];

        $summary = array_fill_keys($fields, 0);

        foreach ($records as $record) {
            foreach ($fields as $field) {
                $summary[$field] += (int) ($record->{$field} ?? 0);
            }
        }

        return $summary;
    }
}
