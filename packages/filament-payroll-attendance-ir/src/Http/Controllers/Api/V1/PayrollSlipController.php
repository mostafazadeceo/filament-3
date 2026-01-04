<?php

namespace Vendor\FilamentPayrollAttendanceIr\Http\Controllers\Api\V1;

use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Vendor\FilamentPayrollAttendanceIr\Http\Resources\PayrollSlipResource;
use Vendor\FilamentPayrollAttendanceIr\Models\PayrollSlip;

class PayrollSlipController extends ApiController
{
    public function __construct()
    {
        $this->authorizeResource(PayrollSlip::class, 'payroll_slip');
    }

    public function index(): AnonymousResourceCollection
    {
        $slips = PayrollSlip::query()->latest()->paginate();

        return PayrollSlipResource::collection($slips);
    }

    public function show(PayrollSlip $payroll_slip): PayrollSlipResource
    {
        $this->logSensitiveAccess($payroll_slip);

        return new PayrollSlipResource($payroll_slip);
    }
}
