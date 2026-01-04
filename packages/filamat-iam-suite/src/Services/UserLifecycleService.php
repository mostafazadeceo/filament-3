<?php

declare(strict_types=1);

namespace Filamat\IamSuite\Services;

use Filamat\IamSuite\Models\Tenant;
use Illuminate\Contracts\Auth\Authenticatable;

class UserLifecycleService
{
    public function __construct(
        protected AuditService $auditService,
        protected SecurityEventService $securityEventService
    ) {}

    public function activate(Tenant $tenant, Authenticatable $user, ?Authenticatable $actor = null, ?string $reason = null): void
    {
        $this->updateMembership($tenant, $user, [
            'status' => 'active',
            'activated_at' => now(),
            'activated_by_id' => $actor?->getAuthIdentifier(),
        ]);

        $this->auditService->log('user.activated', $user, ['reason' => $reason], $actor, $tenant);
        $this->securityEventService->record('user.activated', 'info', $actor, $tenant, [
            'user_id' => $user->getAuthIdentifier(),
        ]);
    }

    public function suspend(Tenant $tenant, Authenticatable $user, ?Authenticatable $actor = null, ?string $reason = null): void
    {
        $this->updateMembership($tenant, $user, [
            'status' => 'inactive',
            'suspended_at' => now(),
            'suspension_reason' => $reason,
            'suspended_by_id' => $actor?->getAuthIdentifier(),
        ]);

        $this->auditService->log('user.suspended', $user, ['reason' => $reason], $actor, $tenant);
        $this->securityEventService->record('user.suspended', 'warning', $actor, $tenant, [
            'user_id' => $user->getAuthIdentifier(),
        ]);
    }

    protected function updateMembership(Tenant $tenant, Authenticatable $user, array $data): void
    {
        if (! method_exists($user, 'tenants')) {
            return;
        }

        $user->tenants()->syncWithoutDetaching([
            $tenant->getKey() => $data,
        ]);
    }
}
