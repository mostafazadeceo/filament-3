<?php

declare(strict_types=1);

namespace Filamat\IamSuite\Events\Iam;

final class IamAccessRequestCreated extends SimpleIamEvent
{
    public static function name(): string
    {
        return 'iam.access_request.created';
    }
}
