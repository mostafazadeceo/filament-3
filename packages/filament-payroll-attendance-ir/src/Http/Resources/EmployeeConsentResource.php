<?php

namespace Vendor\FilamentPayrollAttendanceIr\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class EmployeeConsentResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'company_id' => $this->company_id,
            'branch_id' => $this->branch_id,
            'employee_id' => $this->employee_id,
            'consent_type' => $this->consent_type,
            'is_granted' => (bool) $this->is_granted,
            'granted_by' => $this->granted_by,
            'granted_at' => $this->granted_at,
            'revoked_at' => $this->revoked_at,
            'metadata' => $this->metadata,
        ];
    }
}
