<?php

declare(strict_types=1);

namespace Filamat\IamSuite\Events\Iam;

final class SecurityAuthLogout extends SimpleIamEvent
{
    public static function name(): string
    {
        return 'security.auth.logout';
    }
}
