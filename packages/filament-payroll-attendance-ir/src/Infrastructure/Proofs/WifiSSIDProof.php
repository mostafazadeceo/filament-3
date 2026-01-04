<?php

namespace Vendor\FilamentPayrollAttendanceIr\Infrastructure\Proofs;

class WifiSSIDProof implements LocationProofInterface
{
    /**
     * @param  array<string, mixed>  $payload
     * @return array{is_verified: bool, proof_type: string, proof_payload: array<string, mixed>}
     */
    public function verify(array $payload): array
    {
        return [
            'is_verified' => false,
            'proof_type' => 'wifi_ssid',
            'proof_payload' => [
                'reason' => 'wifi_verification_pending',
            ],
        ];
    }
}
