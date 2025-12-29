<?php

namespace Vendor\FilamentPayrollAttendanceIr\Policies;

use Vendor\FilamentPayrollAttendanceIr\Models\PayrollRun;
use Vendor\FilamentPayrollAttendanceIr\Policies\Concerns\HandlesPayrollPermissions;

class PayrollRunPolicy
{
    use HandlesPayrollPermissions;

    public function viewAny(): bool
    {
        return $this->allow('payroll.run.view');
    }

    public function view(PayrollRun $run): bool
    {
        return $this->allow('payroll.run.view', $run);
    }

    public function create(): bool
    {
        return $this->allow('payroll.run.manage');
    }

    public function update(PayrollRun $run): bool
    {
        return $this->allow('payroll.run.manage', $run);
    }

    public function delete(PayrollRun $run): bool
    {
        return $this->allow('payroll.run.manage', $run);
    }

    public function approve(PayrollRun $run): bool
    {
        return $this->allow('payroll.run.approve', $run);
    }

    public function post(PayrollRun $run): bool
    {
        return $this->allow('payroll.run.post', $run);
    }

    public function lock(PayrollRun $run): bool
    {
        return $this->allow('payroll.run.lock', $run);
    }
}
