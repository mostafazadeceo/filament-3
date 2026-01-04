<?php

namespace Vendor\FilamentPayrollAttendanceIr\Application\Services;

use Vendor\FilamentPayrollAttendanceIr\Domain\Models\AttendancePolicy;

class AttendancePolicyResolver
{
    public function resolve(int $companyId, ?int $branchId): ?AttendancePolicy
    {
        $query = AttendancePolicy::query()
            ->where('company_id', $companyId)
            ->where('status', 'active');

        if ($branchId) {
            $branchPolicy = (clone $query)
                ->where('branch_id', $branchId)
                ->orderByDesc('is_default')
                ->orderByDesc('id')
                ->first();

            if ($branchPolicy) {
                return $branchPolicy;
            }
        }

        $companyDefault = (clone $query)
            ->whereNull('branch_id')
            ->orderByDesc('is_default')
            ->orderByDesc('id')
            ->first();

        return $companyDefault;
    }
}
