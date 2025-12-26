<?php

declare(strict_types=1);

namespace Filamat\IamSuite\Filament\Resources;

use Filamat\IamSuite\Support\IamAuthorization;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Model;

abstract class IamResource extends Resource
{
    protected static ?string $permissionPrefix = null;

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
