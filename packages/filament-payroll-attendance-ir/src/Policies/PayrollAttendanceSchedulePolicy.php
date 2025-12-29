<?php

namespace Vendor\FilamentPayrollAttendanceIr\Policies;

use Vendor\FilamentPayrollAttendanceIr\Models\PayrollAttendanceSchedule;
use Vendor\FilamentPayrollAttendanceIr\Policies\Concerns\HandlesPayrollPermissions;

class PayrollAttendanceSchedulePolicy
{
    use HandlesPayrollPermissions;

    public function viewAny(): bool
    {
        return $this->allow('payroll.schedule.view');
    }

    public function view(PayrollAttendanceSchedule $schedule): bool
    {
        return $this->allow('payroll.schedule.view', $schedule);
    }

    public function create(): bool
    {
        return $this->allow('payroll.schedule.manage');
    }

    public function update(PayrollAttendanceSchedule $schedule): bool
    {
        return $this->allow('payroll.schedule.manage', $schedule);
    }

    public function delete(PayrollAttendanceSchedule $schedule): bool
    {
        return $this->allow('payroll.schedule.manage', $schedule);
    }

    public function restore(PayrollAttendanceSchedule $schedule): bool
    {
        return $this->allow('payroll.schedule.manage', $schedule);
    }

    public function forceDelete(PayrollAttendanceSchedule $schedule): bool
    {
        return $this->allow('payroll.schedule.manage', $schedule);
    }
}
