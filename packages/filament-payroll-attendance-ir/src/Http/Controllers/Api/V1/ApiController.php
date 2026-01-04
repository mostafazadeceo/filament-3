<?php

namespace Vendor\FilamentPayrollAttendanceIr\Http\Controllers\Api\V1;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Routing\Controller;
use Vendor\FilamentPayrollAttendanceIr\Application\Services\SensitiveAccessLogger;

class ApiController extends Controller
{
    use AuthorizesRequests;

    protected function logSensitiveAccess(Model $subject, ?string $reason = null): void
    {
        $reason = $reason ?? request()->header('X-Access-Reason') ?? '';

        if ($reason === '' && config('filament-payroll-attendance-ir.privacy.require_access_reason', false)) {
            abort(422, 'Access reason required.');
        }

        app(SensitiveAccessLogger::class)->log(
            auth()->id(),
            $subject,
            $reason !== '' ? $reason : 'unspecified',
            [
                'path' => request()->path(),
                'method' => request()->method(),
            ]
        );
    }
}
