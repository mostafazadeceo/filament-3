<?php

declare(strict_types=1);

namespace Filamat\IamSuite\Events\Iam;

final class IamPermissionOverrideChanged extends SimpleIamEvent
{
    public static function name(): string
    {
        return 'iam.permission.override.changed';
    }
}
