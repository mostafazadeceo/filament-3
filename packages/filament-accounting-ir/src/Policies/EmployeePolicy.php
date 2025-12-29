<?php

namespace Vendor\FilamentAccountingIr\Policies;

use Vendor\FilamentAccountingIr\Models\Employee;
use Vendor\FilamentAccountingIr\Policies\Concerns\HandlesAccountingPermissions;

class EmployeePolicy
{
    use HandlesAccountingPermissions;

    public function viewAny(): bool
    {
        return $this->allow('accounting.payroll.view');
    }

    public function view(Employee $employee): bool
    {
        return $this->allow('accounting.payroll.view', $employee);
    }

    public function create(): bool
    {
        return $this->allow('accounting.payroll.manage');
    }

    public function update(Employee $employee): bool
    {
        return $this->allow('accounting.payroll.manage', $employee);
    }

    public function delete(Employee $employee): bool
    {
        return $this->allow('accounting.payroll.manage', $employee);
    }
}
