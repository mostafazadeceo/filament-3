<?php

namespace Vendor\FilamentPayrollAttendanceIr\Application\UseCases;

use Vendor\FilamentPayrollAttendanceIr\Domain\Models\MissionRequest;

class RequestMission
{
    /**
     * @param  array<string, mixed>  $payload
     */
    public function execute(array $payload): MissionRequest
    {
        return MissionRequest::query()->create([
            'company_id' => $payload['company_id'],
            'branch_id' => $payload['branch_id'] ?? null,
            'employee_id' => $payload['employee_id'],
            'start_date' => $payload['start_date'],
            'end_date' => $payload['end_date'],
            'allowance_amount' => $payload['allowance_amount'] ?? 0,
            'status' => 'pending',
            'notes' => $payload['notes'] ?? null,
        ]);
    }
}
