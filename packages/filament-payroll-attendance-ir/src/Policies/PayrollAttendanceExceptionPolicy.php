<?php

namespace Vendor\FilamentPayrollAttendanceIr\Policies;

use Vendor\FilamentPayrollAttendanceIr\Domain\Models\AttendanceException;
use Vendor\FilamentPayrollAttendanceIr\Policies\Concerns\HandlesPayrollPermissions;

class PayrollAttendanceExceptionPolicy
{
    use HandlesPayrollPermissions;

    public function viewAny(): bool
    {
        return $this->allow('payroll.exception.view');
    }

    public function view(AttendanceException $record): bool
    {
        return $this->allow('payroll.exception.view', $record);
    }

    public function update(AttendanceException $record): bool
    {
        return $this->allow('payroll.exception.manage', $record);
    }

    public function resolve(AttendanceException $record): bool
    {
        return $this->allow('payroll.exception.resolve', $record);
    }
}
