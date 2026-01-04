<?php

namespace Vendor\FilamentPayrollAttendanceIr\Policies;

use Vendor\FilamentPayrollAttendanceIr\Domain\Models\TimeEvent;
use Vendor\FilamentPayrollAttendanceIr\Policies\Concerns\HandlesPayrollPermissions;

class PayrollTimeEventPolicy
{
    use HandlesPayrollPermissions;

    public function viewAny(): bool
    {
        return $this->allow('payroll.time_event.view');
    }

    public function view(TimeEvent $record): bool
    {
        return $this->allow('payroll.time_event.view', $record);
    }

    public function create(): bool
    {
        return $this->allow('payroll.time_event.manage');
    }

    public function update(TimeEvent $record): bool
    {
        return $this->allow('payroll.time_event.manage', $record);
    }

    public function delete(TimeEvent $record): bool
    {
        return $this->allow('payroll.time_event.manage', $record);
    }
}
