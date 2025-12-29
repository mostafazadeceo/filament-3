<?php

namespace Vendor\FilamentPayrollAttendanceIr\Services;

use Illuminate\Database\Eloquent\Model;
use Vendor\FilamentPayrollAttendanceIr\Models\PayrollAuditEvent;

class PayrollAuditService
{
    /**
     * @param  array<string, mixed>  $metadata
     */
    public function log(string $event, ?Model $subject = null, array $metadata = []): void
    {
        PayrollAuditEvent::query()->create([
            'company_id' => $subject?->company_id,
            'branch_id' => $subject?->branch_id,
            'actor_id' => auth()->id(),
            'event' => $event,
            'subject_type' => $subject ? $subject::class : null,
            'subject_id' => $subject?->getKey(),
            'metadata' => $metadata,
            'created_at' => now(),
        ]);
    }
}
