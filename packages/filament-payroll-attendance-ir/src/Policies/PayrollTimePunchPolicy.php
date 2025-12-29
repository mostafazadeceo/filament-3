<?php

namespace Vendor\FilamentPayrollAttendanceIr\Policies;

use Vendor\FilamentPayrollAttendanceIr\Models\PayrollTimePunch;
use Vendor\FilamentPayrollAttendanceIr\Policies\Concerns\HandlesPayrollPermissions;

class PayrollTimePunchPolicy
{
    use HandlesPayrollPermissions;

    public function viewAny(): bool
    {
        return $this->allow('payroll.punch.view');
    }

    public function view(PayrollTimePunch $punch): bool
    {
        return $this->allow('payroll.punch.view', $punch);
    }

    public function create(): bool
    {
        return $this->allow('payroll.punch.manage');
    }

    public function update(PayrollTimePunch $punch): bool
    {
        return $this->allow('payroll.punch.manage', $punch);
    }

    public function delete(PayrollTimePunch $punch): bool
    {
        return $this->allow('payroll.punch.manage', $punch);
    }
}
