<?php

namespace Vendor\FilamentPayrollAttendanceIr\Infrastructure\Proofs;

interface LocationProofInterface
{
    /**
     * @param  array<string, mixed>  $payload
     * @return array{is_verified: bool, proof_type: string, proof_payload: array<string, mixed>}
     */
    public function verify(array $payload): array;
}
