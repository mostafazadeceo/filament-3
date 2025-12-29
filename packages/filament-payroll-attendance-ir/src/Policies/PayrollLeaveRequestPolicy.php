<?php

namespace Vendor\FilamentPayrollAttendanceIr\Policies;

use Vendor\FilamentPayrollAttendanceIr\Models\PayrollLeaveRequest;
use Vendor\FilamentPayrollAttendanceIr\Policies\Concerns\HandlesPayrollPermissions;

class PayrollLeaveRequestPolicy
{
    use HandlesPayrollPermissions;

    public function viewAny(): bool
    {
        return $this->allow('payroll.leave.view');
    }

    public function view(PayrollLeaveRequest $request): bool
    {
        return $this->allow('payroll.leave.view', $request);
    }

    public function create(): bool
    {
        return $this->allow('payroll.leave.request');
    }

    public function update(PayrollLeaveRequest $request): bool
    {
        return $this->allow('payroll.leave.manage', $request);
    }

    public function delete(PayrollLeaveRequest $request): bool
    {
        return $this->allow('payroll.leave.manage', $request);
    }

    public function approve(PayrollLeaveRequest $request): bool
    {
        return $this->allow('payroll.leave.approve', $request);
    }
}
