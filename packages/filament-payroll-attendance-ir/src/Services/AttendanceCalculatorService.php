<?php

namespace Vendor\FilamentPayrollAttendanceIr\Services;

use Carbon\Carbon;
use Vendor\FilamentPayrollAttendanceIr\Models\PayrollAttendanceRecord;
use Vendor\FilamentPayrollAttendanceIr\Models\PayrollAttendanceSchedule;
use Vendor\FilamentPayrollAttendanceIr\Models\PayrollHoliday;
use Vendor\FilamentPayrollAttendanceIr\Models\PayrollTimePunch;

class AttendanceCalculatorService
{
    public function recalculateForSchedule(PayrollAttendanceSchedule $schedule): PayrollAttendanceRecord
    {
        $workDate = Carbon::parse($schedule->work_date);
        $employeeId = $schedule->employee_id;

        $shift = $schedule->shift;
        $scheduledIn = $shift?->start_time ? $workDate->copy()->setTimeFromTimeString($shift->start_time) : null;
        $scheduledOut = $shift?->end_time ? $workDate->copy()->setTimeFromTimeString($shift->end_time) : null;

        $punches = PayrollTimePunch::query()
            ->where('employee_id', $employeeId)
            ->whereDate('punch_at', $workDate->toDateString())
            ->orderBy('punch_at')
            ->get();

        $actualIn = $punches->firstWhere('type', 'in')?->punch_at;
        $actualOut = $punches->lastWhere('type', 'out')?->punch_at;

        $workedMinutes = 0;
        $lateMinutes = 0;
        $earlyLeaveMinutes = 0;
        $overtimeMinutes = 0;
        $nightMinutes = 0;
        $fridayMinutes = 0;
        $holidayMinutes = 0;

        if ($actualIn && $actualOut) {
            $actualInCarbon = Carbon::parse($actualIn);
            $actualOutCarbon = Carbon::parse($actualOut);
            if ($actualOutCarbon->lessThan($actualInCarbon)) {
                $actualOutCarbon->addDay();
            }

            $workedMinutes = $actualInCarbon->diffInMinutes($actualOutCarbon);
            $breakMinutes = (int) ($shift?->break_minutes ?? 0);
            $workedMinutes = max(0, $workedMinutes - $breakMinutes);

            if ($scheduledIn) {
                $lateMinutes = max(0, $scheduledIn->diffInMinutes($actualInCarbon, false));
            }

            if ($scheduledOut) {
                $earlyLeaveMinutes = max(0, $actualOutCarbon->diffInMinutes($scheduledOut, false));
            }

            if ($scheduledOut) {
                $overtimeMinutes = max(0, $scheduledOut->diffInMinutes($actualOutCarbon, false));
            }

            $nightMinutes = $this->calculateNightMinutes($actualInCarbon, $actualOutCarbon);

            if ($workDate->isFriday()) {
                $fridayMinutes = $workedMinutes;
            }

            if ($this->isHoliday($schedule->company_id, $workDate)) {
                $holidayMinutes = $workedMinutes;
            }
        }

        return PayrollAttendanceRecord::query()->updateOrCreate(
            [
                'employee_id' => $employeeId,
                'work_date' => $workDate->toDateString(),
            ],
            [
                'tenant_id' => $schedule->tenant_id,
                'company_id' => $schedule->company_id,
                'branch_id' => $schedule->branch_id,
                'shift_id' => $schedule->shift_id,
                'scheduled_in' => $scheduledIn,
                'scheduled_out' => $scheduledOut,
                'actual_in' => $actualIn,
                'actual_out' => $actualOut,
                'worked_minutes' => $workedMinutes,
                'late_minutes' => $lateMinutes,
                'early_leave_minutes' => $earlyLeaveMinutes,
                'overtime_minutes' => $overtimeMinutes,
                'night_minutes' => $nightMinutes,
                'friday_minutes' => $fridayMinutes,
                'holiday_minutes' => $holidayMinutes,
                'status' => 'draft',
            ]
        );
    }

    protected function calculateNightMinutes(Carbon $start, Carbon $end): int
    {
        $nightStart = Carbon::parse($start->format('Y-m-d').' '.config('filament-payroll-attendance-ir.attendance.night_start', '22:00'));
        $nightEnd = Carbon::parse($start->format('Y-m-d').' '.config('filament-payroll-attendance-ir.attendance.night_end', '06:00'));

        if ($nightEnd->lessThanOrEqualTo($nightStart)) {
            $nightEnd->addDay();
        }

        if ($end->lessThan($start)) {
            $end = $end->copy()->addDay();
        }

        $overlapStart = $start->greaterThan($nightStart) ? $start : $nightStart;
        $overlapEnd = $end->lessThan($nightEnd) ? $end : $nightEnd;

        if ($overlapEnd->lessThanOrEqualTo($overlapStart)) {
            return 0;
        }

        return $overlapStart->diffInMinutes($overlapEnd);
    }

    protected function isHoliday(int $companyId, Carbon $date): bool
    {
        return PayrollHoliday::query()
            ->where('company_id', $companyId)
            ->whereDate('holiday_date', $date->toDateString())
            ->exists();
    }
}
