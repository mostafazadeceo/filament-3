<?php

namespace Vendor\FilamentPayrollAttendanceIr\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AdvanceResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'company_id' => $this->company_id,
            'branch_id' => $this->branch_id,
            'employee_id' => $this->employee_id,
            'amount' => $this->amount,
            'advance_date' => $this->advance_date,
            'status' => $this->status,
        ];
    }
}
