<?php

declare(strict_types=1);

namespace Filamat\IamSuite\Filament\Resources;

use Filamat\IamSuite\Support\IamAuthorization;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Model;

abstract class IamResource extends Resource
{
    /**
     * We intentionally disable Filament's built-in tenancy scoping.
     *
     * Reason: This codebase mixes tenancy strategies:
     * - Many IAM models are tenant-scoped via `tenant_id` + `TenantScope`.
     * - Some resources (e.g. Spatie Role/Permission) are scoped manually using `tenant_id`.
     * - Some models (e.g. User) relate to tenants via `tenants()` (many-to-many), not `tenant()`.
     *
     * Filament's tenancy scoping assumes a single, consistent relationship name across all models,
     * which doesn't hold here and causes runtime `LogicException`s.
     */
    protected static bool $isScopedToTenant = false;

    protected static ?string $permissionPrefix = null;

    public static function isScopedToTenant(): bool
    {
        return false;
    }

    public static function registerTenancyModelGlobalScope(\Filament\Panel $panel): void
    {
        // IAM resources handle tenancy manually via TenantContext + tenant_id.
        // Avoid Filament's automatic tenancy scope to prevent relationship errors.
    }

    public static function observeTenancyModelCreation(\Filament\Panel $panel): void
    {
        // IAM resources handle tenancy manually.
    }

    /**
     * @return array<string, string>
     */
    protected static function permissionMap(): array
    {
        $prefix = static::$permissionPrefix;
        if (! $prefix) {
            return [];
        }

        return [
            'viewAny' => "{$prefix}.view",
            'view' => "{$prefix}.view",
            'create' => "{$prefix}.manage",
            'update' => "{$prefix}.manage",
            'delete' => "{$prefix}.manage",
            'restore' => "{$prefix}.manage",
            'forceDelete' => "{$prefix}.manage",
        ];
    }

    protected static function authorizeAction(string $action, ?Model $record = null): bool
    {
        $permission = static::permissionMap()[$action] ?? null;
        if (! $permission) {
            return true;
        }

        $tenant = IamAuthorization::resolveTenantFromRecord($record);

        return IamAuthorization::allows($permission, $tenant);
    }

    public static function canViewAny(): bool
    {
        return static::authorizeAction('viewAny');
    }

    public static function canView(Model $record): bool
    {
        return static::authorizeAction('view', $record);
    }

    public static function canCreate(): bool
    {
        return static::authorizeAction('create');
    }

    public static function canEdit(Model $record): bool
    {
        return static::authorizeAction('update', $record);
    }

    public static function canDelete(Model $record): bool
    {
        return static::authorizeAction('delete', $record);
    }
}
