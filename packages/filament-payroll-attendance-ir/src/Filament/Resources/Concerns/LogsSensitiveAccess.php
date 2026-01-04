<?php

namespace Vendor\FilamentPayrollAttendanceIr\Filament\Resources\Concerns;

use Illuminate\Database\Eloquent\Model;
use Vendor\FilamentPayrollAttendanceIr\Application\Services\SensitiveAccessLogger;

trait LogsSensitiveAccess
{
    protected function logSensitiveRecord(Model $record): void
    {
        $reason = request()->get('access_reason') ?? request()->header('X-Access-Reason') ?? '';

        if ($reason === '' && config('filament-payroll-attendance-ir.privacy.require_access_reason', false)) {
            abort(422, 'Access reason required.');
        }

        app(SensitiveAccessLogger::class)->log(
            auth()->id(),
            $record,
            $reason !== '' ? $reason : 'filament_view',
            [
                'path' => request()->path(),
                'method' => request()->method(),
            ]
        );
    }
}
