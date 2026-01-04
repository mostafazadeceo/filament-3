<?php

namespace Vendor\FilamentPayrollAttendanceIr\Policies;

use Vendor\FilamentPayrollAttendanceIr\Domain\Models\WorkCalendar;
use Vendor\FilamentPayrollAttendanceIr\Policies\Concerns\HandlesPayrollPermissions;

class WorkCalendarPolicy
{
    use HandlesPayrollPermissions;

    public function viewAny(): bool
    {
        return $this->allow('payroll.calendar.view');
    }

    public function view(WorkCalendar $record): bool
    {
        return $this->allow('payroll.calendar.view', $record);
    }

    public function create(): bool
    {
        return $this->allow('payroll.calendar.manage');
    }

    public function update(WorkCalendar $record): bool
    {
        return $this->allow('payroll.calendar.manage', $record);
    }

    public function delete(WorkCalendar $record): bool
    {
        return $this->allow('payroll.calendar.manage', $record);
    }
}
