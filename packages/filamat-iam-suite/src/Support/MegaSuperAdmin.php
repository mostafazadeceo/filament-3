<?php

declare(strict_types=1);

namespace Filamat\IamSuite\Support;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Arr;

final class MegaSuperAdmin
{
    public static function check(?Authenticatable $user): bool
    {
        if (! $user) {
            return false;
        }

        $isSuperAdmin = (bool) data_get($user, 'is_super_admin', false);
        if (! $isSuperAdmin) {
            return false;
        }

        $emails = array_map('strtolower', Arr::wrap(config('filamat-iam.mega_super_admins.emails', [])));
        $emails = array_values(array_filter($emails));
        $userIds = array_values(array_filter(Arr::wrap(config('filamat-iam.mega_super_admins.user_ids', []))));

        if ($emails === [] && $userIds === []) {
            return true;
        }

        $userId = $user->getAuthIdentifier();
        if ($userId !== null && in_array($userId, $userIds, true)) {
            return true;
        }

        $email = (string) data_get($user, 'email', '');
        if ($email !== '' && in_array(strtolower($email), $emails, true)) {
            return true;
        }

        return false;
    }
}
