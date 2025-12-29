<?php

namespace Vendor\FilamentPayrollAttendanceIr\Policies;

use Vendor\FilamentPayrollAttendanceIr\Models\PayrollAdvance;
use Vendor\FilamentPayrollAttendanceIr\Policies\Concerns\HandlesPayrollPermissions;

class PayrollAdvancePolicy
{
    use HandlesPayrollPermissions;

    public function viewAny(): bool
    {
        return $this->allow('payroll.advance.manage');
    }

    public function view(PayrollAdvance $advance): bool
    {
        return $this->allow('payroll.advance.manage', $advance);
    }

    public function create(): bool
    {
        return $this->allow('payroll.advance.manage');
    }

    public function update(PayrollAdvance $advance): bool
    {
        return $this->allow('payroll.advance.manage', $advance);
    }

    public function delete(PayrollAdvance $advance): bool
    {
        return $this->allow('payroll.advance.manage', $advance);
    }
}
