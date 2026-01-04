<?php

namespace Vendor\FilamentPayrollAttendanceIr\Policies;

use Vendor\FilamentPayrollAttendanceIr\Domain\Models\EmployeeConsent;
use Vendor\FilamentPayrollAttendanceIr\Policies\Concerns\HandlesPayrollPermissions;

class EmployeeConsentPolicy
{
    use HandlesPayrollPermissions;

    public function viewAny(): bool
    {
        return $this->allow('payroll.consent.view');
    }

    public function view(EmployeeConsent $record): bool
    {
        return $this->allow('payroll.consent.view', $record);
    }

    public function create(): bool
    {
        return $this->allow('payroll.consent.manage');
    }

    public function update(EmployeeConsent $record): bool
    {
        return $this->allow('payroll.consent.manage', $record);
    }

    public function delete(EmployeeConsent $record): bool
    {
        return $this->allow('payroll.consent.manage', $record);
    }
}
