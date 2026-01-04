<?php

namespace Vendor\FilamentPayrollAttendanceIr\Policies;

use Vendor\FilamentPayrollAttendanceIr\Domain\Models\OvertimeRequest;
use Vendor\FilamentPayrollAttendanceIr\Policies\Concerns\HandlesPayrollPermissions;

class OvertimeRequestPolicy
{
    use HandlesPayrollPermissions;

    public function viewAny(): bool
    {
        return $this->allow('payroll.overtime.view');
    }

    public function view(OvertimeRequest $record): bool
    {
        return $this->allow('payroll.overtime.view', $record);
    }

    public function create(): bool
    {
        return $this->allow('payroll.overtime.request');
    }

    public function update(OvertimeRequest $record): bool
    {
        return $this->allow('payroll.overtime.manage', $record);
    }

    public function delete(OvertimeRequest $record): bool
    {
        return $this->allow('payroll.overtime.manage', $record);
    }

    public function approve(OvertimeRequest $record): bool
    {
        return $this->allow('payroll.overtime.approve', $record);
    }
}
