<?php

namespace Vendor\FilamentPayrollAttendanceIr\Policies;

use Vendor\FilamentPayrollAttendanceIr\Models\PayrollInsuranceTable;
use Vendor\FilamentPayrollAttendanceIr\Policies\Concerns\HandlesPayrollPermissions;

class PayrollInsuranceTablePolicy
{
    use HandlesPayrollPermissions;

    public function viewAny(): bool
    {
        return $this->allow('payroll.settings.view');
    }

    public function view(PayrollInsuranceTable $table): bool
    {
        return $this->allow('payroll.settings.view', $table);
    }

    public function create(): bool
    {
        return $this->allow('payroll.settings.manage');
    }

    public function update(PayrollInsuranceTable $table): bool
    {
        return $this->allow('payroll.settings.manage', $table);
    }

    public function delete(PayrollInsuranceTable $table): bool
    {
        return $this->allow('payroll.settings.manage', $table);
    }
}
