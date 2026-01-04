<?php

namespace Vendor\FilamentPayrollAttendanceIr\Policies;

use Vendor\FilamentPayrollAttendanceIr\Domain\Models\SensitiveAccessLog;
use Vendor\FilamentPayrollAttendanceIr\Policies\Concerns\HandlesPayrollPermissions;

class SensitiveAccessLogPolicy
{
    use HandlesPayrollPermissions;

    public function viewAny(): bool
    {
        return $this->allow('payroll.audit.view');
    }

    public function view(SensitiveAccessLog $record): bool
    {
        return $this->allow('payroll.audit.view', $record);
    }

    public function create(): bool
    {
        return false;
    }

    public function update(SensitiveAccessLog $record): bool
    {
        return false;
    }

    public function delete(SensitiveAccessLog $record): bool
    {
        return false;
    }
}
