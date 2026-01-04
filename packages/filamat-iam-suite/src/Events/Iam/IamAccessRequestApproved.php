<?php

declare(strict_types=1);

namespace Filamat\IamSuite\Events\Iam;

final class IamAccessRequestApproved extends SimpleIamEvent
{
    public static function name(): string
    {
        return 'iam.access_request.approved';
    }
}
