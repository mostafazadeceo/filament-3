<?php

namespace Vendor\FilamentAccountingIr\Policies;

use Vendor\FilamentAccountingIr\Models\PayrollTable;
use Vendor\FilamentAccountingIr\Policies\Concerns\HandlesAccountingPermissions;

class PayrollTablePolicy
{
    use HandlesAccountingPermissions;

    public function viewAny(): bool
    {
        return $this->allow('accounting.payroll.view');
    }

    public function view(PayrollTable $record): bool
    {
        return $this->allow('accounting.payroll.view', $record);
    }

    public function create(): bool
    {
        return $this->allow('accounting.payroll.manage');
    }

    public function update(PayrollTable $record): bool
    {
        return $this->allow('accounting.payroll.manage', $record);
    }

    public function delete(PayrollTable $record): bool
    {
        return $this->allow('accounting.payroll.manage', $record);
    }

    public function restore(PayrollTable $record): bool
    {
        return $this->allow('accounting.payroll.manage', $record);
    }

    public function forceDelete(PayrollTable $record): bool
    {
        return $this->allow('accounting.payroll.manage', $record);
    }
}
