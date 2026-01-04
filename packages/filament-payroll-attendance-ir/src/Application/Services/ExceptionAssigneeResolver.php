<?php

namespace Vendor\FilamentPayrollAttendanceIr\Application\Services;

use App\Models\User;
use Filamat\IamSuite\Support\TenantContext;
use Spatie\Permission\PermissionRegistrar;

class ExceptionAssigneeResolver
{
    public function resolve(?int $tenantId = null, ?string $roleName = null): ?int
    {
        $roleName = $roleName ?: (string) config('filament-payroll-attendance-ir.exceptions.default_assignee_role', '');
        if ($roleName === '') {
            return null;
        }

        $tenantId = $tenantId ?: TenantContext::getTenantId();
        if (! $tenantId) {
            return null;
        }

        app(PermissionRegistrar::class)->setPermissionsTeamId($tenantId);

        try {
            return User::role($roleName)->value('id');
        } catch (\Throwable) {
            return null;
        }
    }
}
