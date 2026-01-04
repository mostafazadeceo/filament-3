<?php

namespace Vendor\FilamentPayrollAttendanceIr\Policies;

use Vendor\FilamentPayrollAttendanceIr\Models\PayrollAiLog;
use Vendor\FilamentPayrollAttendanceIr\Policies\Concerns\HandlesPayrollPermissions;

class PayrollAiLogPolicy
{
    use HandlesPayrollPermissions;

    public function viewAny(): bool
    {
        return $this->allow('payroll.ai.view');
    }

    public function view(PayrollAiLog $record): bool
    {
        return $this->allow('payroll.ai.view', $record);
    }

    public function create(): bool
    {
        return false;
    }

    public function update(PayrollAiLog $record): bool
    {
        return false;
    }

    public function delete(PayrollAiLog $record): bool
    {
        return false;
    }
}
