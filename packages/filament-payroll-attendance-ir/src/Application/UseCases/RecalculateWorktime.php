<?php

namespace Vendor\FilamentPayrollAttendanceIr\Application\UseCases;

use Vendor\FilamentPayrollAttendanceIr\Models\PayrollAttendanceRecord;
use Vendor\FilamentPayrollAttendanceIr\Models\PayrollAttendanceSchedule;
use Vendor\FilamentPayrollAttendanceIr\Services\AttendanceCalculatorService;

class RecalculateWorktime
{
    public function __construct(private readonly AttendanceCalculatorService $calculator) {}

    public function execute(PayrollAttendanceSchedule $schedule): PayrollAttendanceRecord
    {
        return $this->calculator->recalculateForSchedule($schedule);
    }
}
