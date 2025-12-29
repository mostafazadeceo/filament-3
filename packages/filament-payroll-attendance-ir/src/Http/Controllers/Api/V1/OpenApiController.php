<?php

namespace Vendor\FilamentPayrollAttendanceIr\Http\Controllers\Api\V1;

use Vendor\FilamentPayrollAttendanceIr\Support\PayrollAttendanceOpenApi;

class OpenApiController extends ApiController
{
    public function show(): array
    {
        return PayrollAttendanceOpenApi::toArray();
    }
}
