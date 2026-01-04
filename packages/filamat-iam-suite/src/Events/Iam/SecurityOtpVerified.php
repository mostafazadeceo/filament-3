<?php

declare(strict_types=1);

namespace Filamat\IamSuite\Events\Iam;

final class SecurityOtpVerified extends SimpleIamEvent
{
    public static function name(): string
    {
        return 'security.otp.verified';
    }
}
