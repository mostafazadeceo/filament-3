<?php

namespace Vendor\FilamentPayrollAttendanceIr\Infrastructure\Biometrics;

class FaceVerificationDriver implements BiometricVerificationInterface
{
    /**
     * @param  array<string, mixed>  $payload
     * @return array{verified: bool, reason: string}
     */
    public function verify(array $payload): array
    {
        return [
            'verified' => false,
            'reason' => 'face_verification_disabled',
        ];
    }
}
