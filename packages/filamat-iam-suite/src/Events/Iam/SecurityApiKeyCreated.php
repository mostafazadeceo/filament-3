<?php

declare(strict_types=1);

namespace Filamat\IamSuite\Events\Iam;

final class SecurityApiKeyCreated extends SimpleIamEvent
{
    public static function name(): string
    {
        return 'security.api_key.created';
    }
}
