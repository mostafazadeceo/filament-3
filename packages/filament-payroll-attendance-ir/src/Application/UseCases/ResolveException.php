<?php

namespace Vendor\FilamentPayrollAttendanceIr\Application\UseCases;

use Vendor\FilamentPayrollAttendanceIr\Domain\Enums\ExceptionStatus;
use Vendor\FilamentPayrollAttendanceIr\Domain\Models\AttendanceException;

class ResolveException
{
    /**
     * @param  array<string, mixed>  $payload
     */
    public function execute(AttendanceException $exception, array $payload = []): AttendanceException
    {
        $requireNotes = (bool) config('filament-payroll-attendance-ir.exceptions.require_resolution_notes', true);
        if ($requireNotes && empty($payload['resolution_notes'])) {
            abort(422, 'Resolution notes required.');
        }

        $exception->update([
            'status' => ExceptionStatus::Resolved,
            'resolved_by' => $payload['resolved_by'] ?? auth()->id(),
            'resolved_at' => $payload['resolved_at'] ?? now(),
            'resolution_notes' => $payload['resolution_notes'] ?? null,
        ]);

        return $exception->refresh();
    }
}
