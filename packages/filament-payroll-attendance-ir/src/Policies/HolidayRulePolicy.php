<?php

namespace Vendor\FilamentPayrollAttendanceIr\Policies;

use Vendor\FilamentPayrollAttendanceIr\Domain\Models\HolidayRule;
use Vendor\FilamentPayrollAttendanceIr\Policies\Concerns\HandlesPayrollPermissions;

class HolidayRulePolicy
{
    use HandlesPayrollPermissions;

    public function viewAny(): bool
    {
        return $this->allow('payroll.calendar.view');
    }

    public function view(HolidayRule $record): bool
    {
        return $this->allow('payroll.calendar.view', $record);
    }

    public function create(): bool
    {
        return $this->allow('payroll.calendar.manage');
    }

    public function update(HolidayRule $record): bool
    {
        return $this->allow('payroll.calendar.manage', $record);
    }

    public function delete(HolidayRule $record): bool
    {
        return $this->allow('payroll.calendar.manage', $record);
    }
}
