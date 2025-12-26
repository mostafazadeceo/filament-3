<?php

namespace Haida\FilamentRelograde\Support;

class RelogradeAuthorization
{
    public static function can(string $permissionKey): bool
    {
        if (! config('relograde.permissions_enabled', false)) {
            return true;
        }

        $permission = config('relograde.permissions.'.$permissionKey);

        if (! $permission) {
            return true;
        }

        $user = auth()->user();
        if (! $user) {
            return false;
        }

        return $user->can($permission);
    }
}
