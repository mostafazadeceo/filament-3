<?php

namespace Vendor\FilamentPayrollAttendanceIr\Policies;

use Vendor\FilamentPayrollAttendanceIr\Models\PayrollEmployee;
use Vendor\FilamentPayrollAttendanceIr\Policies\Concerns\HandlesPayrollPermissions;

class PayrollEmployeePolicy
{
    use HandlesPayrollPermissions;

    public function viewAny(): bool
    {
        return $this->allow('payroll.employee.view');
    }

    public function view(PayrollEmployee $employee): bool
    {
        return $this->allow('payroll.employee.view', $employee);
    }

    public function create(): bool
    {
        return $this->allow('payroll.employee.manage');
    }

    public function update(PayrollEmployee $employee): bool
    {
        return $this->allow('payroll.employee.manage', $employee);
    }

    public function delete(PayrollEmployee $employee): bool
    {
        return $this->allow('payroll.employee.manage', $employee);
    }

    public function restore(PayrollEmployee $employee): bool
    {
        return $this->allow('payroll.employee.manage', $employee);
    }

    public function forceDelete(PayrollEmployee $employee): bool
    {
        return $this->allow('payroll.employee.manage', $employee);
    }
}
