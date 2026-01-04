<?php

namespace Vendor\FilamentPayrollAttendanceIr\Infrastructure\Biometrics;

interface BiometricVerificationInterface
{
    /**
     * @param  array<string, mixed>  $payload
     * @return array{verified: bool, reason: string}
     */
    public function verify(array $payload): array;
}
