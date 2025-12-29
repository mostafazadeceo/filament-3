<?php

namespace Vendor\FilamentPayrollAttendanceIr\Policies;

use Vendor\FilamentPayrollAttendanceIr\Models\PayrollAttendanceRecord;
use Vendor\FilamentPayrollAttendanceIr\Policies\Concerns\HandlesPayrollPermissions;

class PayrollAttendanceRecordPolicy
{
    use HandlesPayrollPermissions;

    public function viewAny(): bool
    {
        return $this->allow('payroll.attendance.view');
    }

    public function view(PayrollAttendanceRecord $record): bool
    {
        return $this->allow('payroll.attendance.view', $record);
    }

    public function create(): bool
    {
        return $this->allow('payroll.attendance.manage');
    }

    public function update(PayrollAttendanceRecord $record): bool
    {
        return $this->allow('payroll.attendance.manage', $record);
    }

    public function delete(PayrollAttendanceRecord $record): bool
    {
        return $this->allow('payroll.attendance.manage', $record);
    }

    public function approve(PayrollAttendanceRecord $record): bool
    {
        return $this->allow('payroll.attendance.approve', $record);
    }

    public function lock(PayrollAttendanceRecord $record): bool
    {
        return $this->allow('payroll.attendance.lock', $record);
    }
}
