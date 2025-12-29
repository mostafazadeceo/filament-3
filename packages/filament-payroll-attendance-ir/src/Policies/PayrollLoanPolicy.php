<?php

namespace Vendor\FilamentPayrollAttendanceIr\Policies;

use Vendor\FilamentPayrollAttendanceIr\Models\PayrollLoan;
use Vendor\FilamentPayrollAttendanceIr\Policies\Concerns\HandlesPayrollPermissions;

class PayrollLoanPolicy
{
    use HandlesPayrollPermissions;

    public function viewAny(): bool
    {
        return $this->allow('payroll.loan.manage');
    }

    public function view(PayrollLoan $loan): bool
    {
        return $this->allow('payroll.loan.manage', $loan);
    }

    public function create(): bool
    {
        return $this->allow('payroll.loan.manage');
    }

    public function update(PayrollLoan $loan): bool
    {
        return $this->allow('payroll.loan.manage', $loan);
    }

    public function delete(PayrollLoan $loan): bool
    {
        return $this->allow('payroll.loan.manage', $loan);
    }
}
