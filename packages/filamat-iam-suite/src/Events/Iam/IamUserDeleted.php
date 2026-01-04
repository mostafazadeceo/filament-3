<?php

declare(strict_types=1);

namespace Filamat\IamSuite\Events\Iam;

final class IamUserDeleted extends SimpleIamEvent
{
    public static function name(): string
    {
        return 'iam.user.deleted';
    }
}
