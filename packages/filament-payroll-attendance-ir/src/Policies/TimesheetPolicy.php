<?php

namespace Vendor\FilamentPayrollAttendanceIr\Policies;

use Vendor\FilamentPayrollAttendanceIr\Domain\Models\Timesheet;
use Vendor\FilamentPayrollAttendanceIr\Policies\Concerns\HandlesPayrollPermissions;

class TimesheetPolicy
{
    use HandlesPayrollPermissions;

    public function viewAny(): bool
    {
        return $this->allow('payroll.timesheet.view');
    }

    public function view(Timesheet $record): bool
    {
        return $this->allow('payroll.timesheet.view', $record);
    }

    public function create(): bool
    {
        return $this->allow('payroll.timesheet.manage');
    }

    public function update(Timesheet $record): bool
    {
        return $this->allow('payroll.timesheet.manage', $record);
    }

    public function delete(Timesheet $record): bool
    {
        return $this->allow('payroll.timesheet.manage', $record);
    }

    public function approve(Timesheet $record): bool
    {
        return $this->allow('payroll.timesheet.approve', $record);
    }
}
