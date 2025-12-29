<?php

namespace Vendor\FilamentPayrollAttendanceIr\Policies;

use Vendor\FilamentPayrollAttendanceIr\Models\PayrollMinimumWageTable;
use Vendor\FilamentPayrollAttendanceIr\Policies\Concerns\HandlesPayrollPermissions;

class PayrollMinimumWageTablePolicy
{
    use HandlesPayrollPermissions;

    public function viewAny(): bool
    {
        return $this->allow('payroll.settings.view');
    }

    public function view(PayrollMinimumWageTable $table): bool
    {
        return $this->allow('payroll.settings.view', $table);
    }

    public function create(): bool
    {
        return $this->allow('payroll.settings.manage');
    }

    public function update(PayrollMinimumWageTable $table): bool
    {
        return $this->allow('payroll.settings.manage', $table);
    }

    public function delete(PayrollMinimumWageTable $table): bool
    {
        return $this->allow('payroll.settings.manage', $table);
    }
}
