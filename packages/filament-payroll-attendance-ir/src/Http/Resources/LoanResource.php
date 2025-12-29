<?php

namespace Vendor\FilamentPayrollAttendanceIr\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class LoanResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'company_id' => $this->company_id,
            'branch_id' => $this->branch_id,
            'employee_id' => $this->employee_id,
            'amount' => $this->amount,
            'installment_count' => $this->installment_count,
            'installment_amount' => $this->installment_amount,
            'start_date' => $this->start_date,
            'status' => $this->status,
        ];
    }
}
