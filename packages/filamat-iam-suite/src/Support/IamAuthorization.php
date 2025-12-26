<?php

declare(strict_types=1);

namespace Filamat\IamSuite\Support;

use Filamat\IamSuite\Models\Tenant;
use Filamat\IamSuite\Services\AccessService;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;

final class IamAuthorization
{
    public static function allows(string $permission, ?Tenant $tenant = null, ?Authenticatable $user = null): bool
    {
        $user ??= auth()->user();
        if (! $user) {
            return false;
        }

        if (method_exists($user, 'hasIamSuiteSuperAdmin') && $user->hasIamSuiteSuperAdmin() && TenantContext::shouldBypass()) {
            return true;
        }

        $tenant ??= TenantContext::getTenant();

        if ($tenant) {
            return app(AccessService::class)->checkPermission($user, $tenant, $permission);
        }

        if (method_exists($user, 'hasPermissionTo')) {
            try {
                return $user->hasPermissionTo($permission);
            } catch (\Throwable) {
                return false;
            }
        }

        return false;
    }

    /**
     * @param  array<int, string>  $permissions
     */
    public static function allowsAny(array $permissions, ?Tenant $tenant = null, ?Authenticatable $user = null): bool
    {
        foreach ($permissions as $permission) {
            if (self::allows($permission, $tenant, $user)) {
                return true;
            }
        }

        return false;
    }

    public static function resolveTenantFromRecord(?Model $record = null): ?Tenant
    {
        if ($record instanceof Tenant) {
            return $record;
        }

        if ($record && method_exists($record, 'tenant')) {
            $tenant = $record->tenant;
            if ($tenant instanceof Tenant) {
                return $tenant;
            }
        }

        if ($record && method_exists($record, 'wallet')) {
            $wallet = $record->wallet;
            if ($wallet && method_exists($wallet, 'tenant')) {
                $tenant = $wallet->tenant;
                if ($tenant instanceof Tenant) {
                    return $tenant;
                }
            }
        }

        if ($record && $record->getAttribute('tenant_id')) {
            return Tenant::query()->find($record->getAttribute('tenant_id'));
        }

        return TenantContext::getTenant();
    }
}
