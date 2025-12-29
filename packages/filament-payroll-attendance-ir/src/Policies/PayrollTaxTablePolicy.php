<?php

namespace Vendor\FilamentPayrollAttendanceIr\Policies;

use Vendor\FilamentPayrollAttendanceIr\Models\PayrollTaxTable;
use Vendor\FilamentPayrollAttendanceIr\Policies\Concerns\HandlesPayrollPermissions;

class PayrollTaxTablePolicy
{
    use HandlesPayrollPermissions;

    public function viewAny(): bool
    {
        return $this->allow('payroll.settings.view');
    }

    public function view(PayrollTaxTable $table): bool
    {
        return $this->allow('payroll.settings.view', $table);
    }

    public function create(): bool
    {
        return $this->allow('payroll.settings.manage');
    }

    public function update(PayrollTaxTable $table): bool
    {
        return $this->allow('payroll.settings.manage', $table);
    }

    public function delete(PayrollTaxTable $table): bool
    {
        return $this->allow('payroll.settings.manage', $table);
    }
}
