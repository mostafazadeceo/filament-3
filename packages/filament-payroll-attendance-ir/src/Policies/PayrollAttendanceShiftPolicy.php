<?php

namespace Vendor\FilamentPayrollAttendanceIr\Policies;

use Vendor\FilamentPayrollAttendanceIr\Models\PayrollAttendanceShift;
use Vendor\FilamentPayrollAttendanceIr\Policies\Concerns\HandlesPayrollPermissions;

class PayrollAttendanceShiftPolicy
{
    use HandlesPayrollPermissions;

    public function viewAny(): bool
    {
        return $this->allow('payroll.shift.view');
    }

    public function view(PayrollAttendanceShift $shift): bool
    {
        return $this->allow('payroll.shift.view', $shift);
    }

    public function create(): bool
    {
        return $this->allow('payroll.shift.manage');
    }

    public function update(PayrollAttendanceShift $shift): bool
    {
        return $this->allow('payroll.shift.manage', $shift);
    }

    public function delete(PayrollAttendanceShift $shift): bool
    {
        return $this->allow('payroll.shift.manage', $shift);
    }

    public function restore(PayrollAttendanceShift $shift): bool
    {
        return $this->allow('payroll.shift.manage', $shift);
    }

    public function forceDelete(PayrollAttendanceShift $shift): bool
    {
        return $this->allow('payroll.shift.manage', $shift);
    }
}
