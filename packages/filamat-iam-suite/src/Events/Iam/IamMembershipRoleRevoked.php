<?php

declare(strict_types=1);

namespace Filamat\IamSuite\Events\Iam;

final class IamMembershipRoleRevoked extends SimpleIamEvent
{
    public static function name(): string
    {
        return 'iam.membership.role.revoked';
    }
}
