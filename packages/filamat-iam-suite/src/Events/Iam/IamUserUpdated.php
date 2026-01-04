<?php

declare(strict_types=1);

namespace Filamat\IamSuite\Events\Iam;

final class IamUserUpdated extends SimpleIamEvent
{
    public static function name(): string
    {
        return 'iam.user.updated';
    }
}
