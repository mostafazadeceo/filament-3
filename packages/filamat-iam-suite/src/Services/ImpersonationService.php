<?php

declare(strict_types=1);

namespace Filamat\IamSuite\Services;

use Filamat\IamSuite\Models\Tenant;
use Filamat\IamSuite\Support\TenantContext;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Auth;
use RuntimeException;

class ImpersonationService
{
    public const SESSION_IMPERSONATOR = 'filamat_iam_impersonator_id';

    public const SESSION_IMPERSONATED = 'filamat_iam_impersonated_id';

    public const SESSION_IMPERSONATED_TENANT = 'filamat_iam_impersonated_tenant_id';

    public const SESSION_IMPERSONATED_AT = 'filamat_iam_impersonated_at';

    public function __construct(
        protected AuditService $auditService,
        protected SecurityEventService $securityEventService
    ) {}

    public function start(Authenticatable $actor, Authenticatable $target, ?Tenant $tenant = null): void
    {
        if (! (bool) config('filamat-iam.impersonation.enabled', true)) {
            throw new RuntimeException('امپرسونیشن غیرفعال است.');
        }

        if (! method_exists($actor, 'hasIamSuiteSuperAdmin') || ! $actor->hasIamSuiteSuperAdmin()) {
            throw new RuntimeException('اجازه امپرسونیشن ندارید.');
        }

        session()->put(self::SESSION_IMPERSONATOR, $actor->getAuthIdentifier());
        session()->put(self::SESSION_IMPERSONATED, $target->getAuthIdentifier());
        session()->put(self::SESSION_IMPERSONATED_TENANT, $tenant?->getKey());
        session()->put(self::SESSION_IMPERSONATED_AT, now()->toISOString());

        Auth::login($target);
        TenantContext::setTenant($tenant);

        $this->auditService->log('impersonation.start', $target, [
            'actor_id' => $actor->getAuthIdentifier(),
            'tenant_id' => $tenant?->getKey(),
        ], $actor, $tenant);

        $this->securityEventService->record('impersonation.start', 'warning', $actor, $tenant, [
            'target_id' => $target->getAuthIdentifier(),
        ]);
    }

    public function stop(): void
    {
        $impersonatorId = session()->get(self::SESSION_IMPERSONATOR);
        $impersonatedId = session()->get(self::SESSION_IMPERSONATED);
        $tenantId = session()->get(self::SESSION_IMPERSONATED_TENANT);

        if (! $impersonatorId) {
            return;
        }

        $userModel = config('auth.providers.users.model');
        $impersonator = $userModel::query()->find($impersonatorId);
        $impersonated = $impersonatedId ? $userModel::query()->find($impersonatedId) : null;
        $tenant = $tenantId ? Tenant::query()->find($tenantId) : null;

        session()->forget([
            self::SESSION_IMPERSONATOR,
            self::SESSION_IMPERSONATED,
            self::SESSION_IMPERSONATED_TENANT,
            self::SESSION_IMPERSONATED_AT,
        ]);

        if ($impersonator) {
            Auth::login($impersonator);
        }

        TenantContext::setTenant(null);

        if ($impersonated) {
            $this->auditService->log('impersonation.stop', $impersonated, [
                'actor_id' => $impersonator?->getKey(),
                'tenant_id' => $tenant?->getKey(),
            ], $impersonator, $tenant);

            $this->securityEventService->record('impersonation.stop', 'info', $impersonator, $tenant, [
                'target_id' => $impersonated->getKey(),
            ]);
        }
    }

    public function isImpersonating(): bool
    {
        return session()->has(self::SESSION_IMPERSONATOR);
    }
}
