<?php

namespace Vendor\FilamentPayrollAttendanceIr\Policies;

use Vendor\FilamentPayrollAttendanceIr\Models\PayrollContract;
use Vendor\FilamentPayrollAttendanceIr\Policies\Concerns\HandlesPayrollPermissions;

class PayrollContractPolicy
{
    use HandlesPayrollPermissions;

    public function viewAny(): bool
    {
        return $this->allow('payroll.contract.view');
    }

    public function view(PayrollContract $contract): bool
    {
        return $this->allow('payroll.contract.view', $contract);
    }

    public function create(): bool
    {
        return $this->allow('payroll.contract.manage');
    }

    public function update(PayrollContract $contract): bool
    {
        return $this->allow('payroll.contract.manage', $contract);
    }

    public function delete(PayrollContract $contract): bool
    {
        return $this->allow('payroll.contract.manage', $contract);
    }

    public function restore(PayrollContract $contract): bool
    {
        return $this->allow('payroll.contract.manage', $contract);
    }

    public function forceDelete(PayrollContract $contract): bool
    {
        return $this->allow('payroll.contract.manage', $contract);
    }
}
