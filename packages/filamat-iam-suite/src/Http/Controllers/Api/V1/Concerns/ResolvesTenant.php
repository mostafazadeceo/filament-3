<?php

declare(strict_types=1);

namespace Filamat\IamSuite\Http\Controllers\Api\V1\Concerns;

use Filamat\IamSuite\Models\Tenant;
use Filamat\IamSuite\Support\TenantContext;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Spatie\Permission\Models\Role;

trait ResolvesTenant
{
    protected function resolveTenant(Request $request): ?Tenant
    {
        $tenant = TenantContext::getTenant();
        if ($tenant) {
            return $tenant;
        }

        $tenantId = $request->integer('tenant_id')
            ?: (int) $request->header(config('filamat-iam.api.tenant_header', 'X-Tenant-ID'));

        return $tenantId ? Tenant::query()->find($tenantId) : null;
    }

    protected function ensureTenantRequest(Request $request, ?Tenant $tenant): ?Response
    {
        if (! $tenant) {
            return response(['message' => 'فضای کاری یافت نشد.'], 422);
        }

        if (TenantContext::shouldBypass()) {
            return null;
        }

        $explicitTenantId = $request->integer('tenant_id')
            ?: (int) $request->header(config('filamat-iam.api.tenant_header', 'X-Tenant-ID'));

        if ($explicitTenantId && $explicitTenantId !== (int) $tenant->getKey()) {
            return response(['message' => 'فضای کاری نامعتبر است.'], 422);
        }

        return null;
    }

    protected function userBelongsToTenant(Authenticatable $user, Tenant $tenant): bool
    {
        if (! method_exists($user, 'tenants')) {
            return true;
        }

        return $user->tenants()->where('tenants.id', $tenant->getKey())->exists();
    }

    protected function findTenantRole(int $roleId, Tenant $tenant): ?Role
    {
        if (TenantContext::shouldBypass()) {
            return Role::query()->find($roleId);
        }

        return Role::query()
            ->where('id', $roleId)
            ->where('tenant_id', $tenant->getKey())
            ->first();
    }
}
