<?php

declare(strict_types=1);

namespace Filamat\IamSuite\Events\Iam;

final class SecurityImpersonationStopped extends SimpleIamEvent
{
    public static function name(): string
    {
        return 'security.impersonation.stopped';
    }
}
