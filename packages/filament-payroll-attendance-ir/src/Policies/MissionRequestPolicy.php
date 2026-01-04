<?php

namespace Vendor\FilamentPayrollAttendanceIr\Policies;

use Vendor\FilamentPayrollAttendanceIr\Models\PayrollMission;
use Vendor\FilamentPayrollAttendanceIr\Policies\Concerns\HandlesPayrollPermissions;

class MissionRequestPolicy
{
    use HandlesPayrollPermissions;

    public function viewAny(): bool
    {
        return $this->allow('payroll.mission.view');
    }

    public function view(PayrollMission $record): bool
    {
        return $this->allow('payroll.mission.view', $record);
    }

    public function create(): bool
    {
        return $this->allow('payroll.mission.request');
    }

    public function update(PayrollMission $record): bool
    {
        return $this->allow('payroll.mission.manage', $record);
    }

    public function delete(PayrollMission $record): bool
    {
        return $this->allow('payroll.mission.manage', $record);
    }

    public function approve(PayrollMission $record): bool
    {
        return $this->allow('payroll.mission.approve', $record);
    }
}
