<?php

namespace Vendor\FilamentPayrollAttendanceIr\Http\Controllers\Api\V1;

use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Vendor\FilamentPayrollAttendanceIr\Http\Resources\PayrollAiLogResource;
use Vendor\FilamentPayrollAttendanceIr\Models\PayrollAiLog;

class PayrollAiLogController extends ApiController
{
    public function __construct()
    {
        $this->authorizeResource(PayrollAiLog::class, 'payroll_ai_log');
    }

    public function index(): AnonymousResourceCollection
    {
        $logs = PayrollAiLog::query()->latest('created_at')->paginate();

        return PayrollAiLogResource::collection($logs);
    }

    public function show(PayrollAiLog $payroll_ai_log): PayrollAiLogResource
    {
        return new PayrollAiLogResource($payroll_ai_log);
    }
}
