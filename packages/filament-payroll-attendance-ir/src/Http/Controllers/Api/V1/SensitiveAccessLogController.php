<?php

namespace Vendor\FilamentPayrollAttendanceIr\Http\Controllers\Api\V1;

use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Vendor\FilamentPayrollAttendanceIr\Domain\Models\SensitiveAccessLog;
use Vendor\FilamentPayrollAttendanceIr\Http\Resources\SensitiveAccessLogResource;

class SensitiveAccessLogController extends ApiController
{
    public function __construct()
    {
        $this->authorizeResource(SensitiveAccessLog::class, 'sensitive_access_log');
    }

    public function index(): AnonymousResourceCollection
    {
        $logs = SensitiveAccessLog::query()->latest('created_at')->paginate();

        return SensitiveAccessLogResource::collection($logs);
    }

    public function show(SensitiveAccessLog $sensitive_access_log): SensitiveAccessLogResource
    {
        return new SensitiveAccessLogResource($sensitive_access_log);
    }
}
