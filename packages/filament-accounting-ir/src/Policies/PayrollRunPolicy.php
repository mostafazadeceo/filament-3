<?php

namespace Vendor\FilamentAccountingIr\Policies;

use Vendor\FilamentAccountingIr\Models\PayrollRun;
use Vendor\FilamentAccountingIr\Policies\Concerns\HandlesAccountingPermissions;

class PayrollRunPolicy
{
    use HandlesAccountingPermissions;

    public function viewAny(): bool
    {
        return $this->allow('accounting.payroll.view');
    }

    public function view(PayrollRun $record): bool
    {
        return $this->allow('accounting.payroll.view', $record);
    }

    public function create(): bool
    {
        return $this->allow('accounting.payroll.manage');
    }

    public function update(PayrollRun $record): bool
    {
        return $this->allow('accounting.payroll.manage', $record);
    }

    public function delete(PayrollRun $record): bool
    {
        return $this->allow('accounting.payroll.manage', $record);
    }

    public function restore(PayrollRun $record): bool
    {
        return $this->allow('accounting.payroll.manage', $record);
    }

    public function forceDelete(PayrollRun $record): bool
    {
        return $this->allow('accounting.payroll.manage', $record);
    }
}
