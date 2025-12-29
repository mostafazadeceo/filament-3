<?php

namespace Vendor\FilamentPayrollAttendanceIr\Policies;

use Vendor\FilamentPayrollAttendanceIr\Models\PayrollLeaveType;
use Vendor\FilamentPayrollAttendanceIr\Policies\Concerns\HandlesPayrollPermissions;

class PayrollLeaveTypePolicy
{
    use HandlesPayrollPermissions;

    public function viewAny(): bool
    {
        return $this->allow('payroll.leave.view');
    }

    public function view(PayrollLeaveType $type): bool
    {
        return $this->allow('payroll.leave.view', $type);
    }

    public function create(): bool
    {
        return $this->allow('payroll.leave.manage');
    }

    public function update(PayrollLeaveType $type): bool
    {
        return $this->allow('payroll.leave.manage', $type);
    }

    public function delete(PayrollLeaveType $type): bool
    {
        return $this->allow('payroll.leave.manage', $type);
    }
}
