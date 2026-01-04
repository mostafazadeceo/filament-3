<?php

declare(strict_types=1);

namespace Filamat\IamSuite\Events\Iam;

final class SecurityApiKeyRevoked extends SimpleIamEvent
{
    public static function name(): string
    {
        return 'security.api_key.revoked';
    }
}
