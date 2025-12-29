<?php

namespace Vendor\FilamentPayrollAttendanceIr\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PayrollRunResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'company_id' => $this->company_id,
            'branch_id' => $this->branch_id,
            'period_start' => $this->period_start,
            'period_end' => $this->period_end,
            'status' => $this->status,
            'approved_at' => $this->approved_at,
            'posted_at' => $this->posted_at,
            'locked_at' => $this->locked_at,
            'created_at' => $this->created_at,
        ];
    }
}
