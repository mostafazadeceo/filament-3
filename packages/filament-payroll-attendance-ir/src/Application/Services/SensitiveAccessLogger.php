<?php

namespace Vendor\FilamentPayrollAttendanceIr\Application\Services;

use Illuminate\Database\Eloquent\Model;
use Vendor\FilamentPayrollAttendanceIr\Domain\Models\SensitiveAccessLog;

class SensitiveAccessLogger
{
    /**
     * @param  array<string, mixed>  $metadata
     */
    public function log(?int $actorId, ?Model $subject, string $reason, array $metadata = []): void
    {
        SensitiveAccessLog::query()->create([
            'company_id' => $subject?->company_id,
            'branch_id' => $subject?->branch_id,
            'actor_id' => $actorId,
            'subject_type' => $subject ? $subject::class : null,
            'subject_id' => $subject?->getKey(),
            'reason' => $reason,
            'metadata' => $metadata,
            'created_at' => now(),
        ]);
    }
}
