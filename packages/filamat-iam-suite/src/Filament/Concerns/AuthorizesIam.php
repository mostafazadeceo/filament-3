<?php

declare(strict_types=1);

namespace Filamat\IamSuite\Filament\Concerns;

use Filamat\IamSuite\Support\IamAuthorization;

trait AuthorizesIam
{
    public static function canAccess(): bool
    {
        if (! property_exists(static::class, 'permission')) {
            return true;
        }

        $permission = static::$permission;
        if (! $permission) {
            return true;
        }

        return IamAuthorization::allows($permission);
    }

    public static function canView(): bool
    {
        return static::canAccess();
    }
}
