<?php

namespace Vendor\FilamentPayrollAttendanceIr\Infrastructure\Proofs;

class GeoFenceProof implements LocationProofInterface
{
    /**
     * @param  array<string, mixed>  $payload
     * @return array{is_verified: bool, proof_type: string, proof_payload: array<string, mixed>}
     */
    public function verify(array $payload): array
    {
        return [
            'is_verified' => false,
            'proof_type' => 'geofence',
            'proof_payload' => [
                'reason' => 'geofence_verification_pending',
            ],
        ];
    }
}
