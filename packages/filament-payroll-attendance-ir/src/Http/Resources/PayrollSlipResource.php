<?php

namespace Vendor\FilamentPayrollAttendanceIr\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PayrollSlipResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'payroll_run_id' => $this->payroll_run_id,
            'employee_id' => $this->employee_id,
            'scope' => $this->scope,
            'status' => $this->status,
            'gross_amount' => $this->gross_amount,
            'deductions_amount' => $this->deductions_amount,
            'net_amount' => $this->net_amount,
            'insurance_employee_amount' => $this->insurance_employee_amount,
            'insurance_employer_amount' => $this->insurance_employer_amount,
            'tax_amount' => $this->tax_amount,
        ];
    }
}
