<?php

declare(strict_types=1);

namespace Filamat\IamSuite\Services;

use Filamat\IamSuite\Models\PrivilegeEligibility;
use Filamat\IamSuite\Models\Tenant;
use Illuminate\Contracts\Auth\Authenticatable;
use Spatie\Permission\Models\Role;

class PrivilegeEligibilityService
{
    public function __construct(
        protected AuditService $auditService,
        protected SecurityEventService $securityEventService
    ) {}

    public function grant(Tenant $tenant, Authenticatable $user, Role $role, ?Authenticatable $actor = null, ?string $reason = null): PrivilegeEligibility
    {
        $eligibility = PrivilegeEligibility::query()->updateOrCreate([
            'tenant_id' => $tenant->getKey(),
            'user_id' => $user->getAuthIdentifier(),
            'role_id' => $role->getKey(),
        ], [
            'eligible_by_id' => $actor?->getAuthIdentifier(),
            'can_request' => true,
            'active' => true,
            'reason' => $reason,
        ]);

        $this->auditService->log('pam.eligibility.granted', $eligibility, ['reason' => $reason], $actor, $tenant);
        $this->securityEventService->record('pam.eligibility.granted', 'info', $actor, $tenant, [
            'eligibility_id' => $eligibility->getKey(),
        ]);

        return $eligibility;
    }

    public function revoke(PrivilegeEligibility $eligibility, ?Authenticatable $actor = null, ?string $reason = null): PrivilegeEligibility
    {
        $eligibility->update([
            'active' => false,
            'reason' => $reason,
        ]);

        $tenant = $eligibility->tenant;
        $this->auditService->log('pam.eligibility.revoked', $eligibility, ['reason' => $reason], $actor, $tenant);
        $this->securityEventService->record('pam.eligibility.revoked', 'warning', $actor, $tenant, [
            'eligibility_id' => $eligibility->getKey(),
        ]);

        return $eligibility;
    }

    public function isEligible(Tenant $tenant, Authenticatable $user, Role $role): bool
    {
        return PrivilegeEligibility::query()
            ->where('tenant_id', $tenant->getKey())
            ->where('user_id', $user->getAuthIdentifier())
            ->where('role_id', $role->getKey())
            ->where('active', true)
            ->exists();
    }
}
