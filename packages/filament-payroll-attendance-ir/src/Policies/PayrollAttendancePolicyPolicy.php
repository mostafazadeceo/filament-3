<?php

namespace Vendor\FilamentPayrollAttendanceIr\Policies;

use Vendor\FilamentPayrollAttendanceIr\Domain\Models\AttendancePolicy;
use Vendor\FilamentPayrollAttendanceIr\Policies\Concerns\HandlesPayrollPermissions;

class PayrollAttendancePolicyPolicy
{
    use HandlesPayrollPermissions;

    public function viewAny(): bool
    {
        return $this->allow('payroll.policy.view');
    }

    public function view(AttendancePolicy $record): bool
    {
        return $this->allow('payroll.policy.view', $record);
    }

    public function create(): bool
    {
        return $this->allow('payroll.policy.manage');
    }

    public function update(AttendancePolicy $record): bool
    {
        return $this->allow('payroll.policy.manage', $record);
    }

    public function delete(AttendancePolicy $record): bool
    {
        return $this->allow('payroll.policy.manage', $record);
    }
}
