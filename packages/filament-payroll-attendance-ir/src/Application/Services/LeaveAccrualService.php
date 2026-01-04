<?php

namespace Vendor\FilamentPayrollAttendanceIr\Application\Services;

use Vendor\FilamentPayrollAttendanceIr\Domain\Models\AttendancePolicy;
use Vendor\FilamentPayrollAttendanceIr\Models\PayrollLeaveType;

class LeaveAccrualService
{
    public function calculateAnnualAccrual(PayrollLeaveType $leaveType, ?AttendancePolicy $policy = null): float
    {
        $base = (float) ($leaveType->default_days_per_year ?? 0);
        $rules = array_merge(
            (array) config('filament-payroll-attendance-ir.policy.default_rules', []),
            $policy?->rules ?? []
        );

        $overrides = (array) ($rules['leave_accrual_overrides'] ?? []);
        $key = $leaveType->code ?: (string) $leaveType->getKey();

        if (array_key_exists($key, $overrides)) {
            return (float) $overrides[$key];
        }

        $multiplier = $rules['leave_accrual_multiplier'] ?? null;
        if ($multiplier !== null) {
            return $base * (float) $multiplier;
        }

        return $base;
    }
}
