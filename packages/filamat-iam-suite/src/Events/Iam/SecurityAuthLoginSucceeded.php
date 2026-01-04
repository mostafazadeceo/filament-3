<?php

declare(strict_types=1);

namespace Filamat\IamSuite\Events\Iam;

final class SecurityAuthLoginSucceeded extends SimpleIamEvent
{
    public static function name(): string
    {
        return 'security.auth.login.succeeded';
    }
}
