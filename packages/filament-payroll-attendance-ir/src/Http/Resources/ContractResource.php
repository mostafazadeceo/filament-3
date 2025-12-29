<?php

namespace Vendor\FilamentPayrollAttendanceIr\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ContractResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'company_id' => $this->company_id,
            'branch_id' => $this->branch_id,
            'employee_id' => $this->employee_id,
            'scope' => $this->scope,
            'status' => $this->status,
            'effective_from' => $this->effective_from,
            'effective_to' => $this->effective_to,
            'base_salary' => $this->base_salary,
            'daily_hours' => $this->daily_hours,
            'weekly_hours' => $this->weekly_hours,
            'monthly_hours' => $this->monthly_hours,
            'housing_allowance' => $this->housing_allowance,
            'food_allowance' => $this->food_allowance,
            'child_allowance' => $this->child_allowance,
            'marriage_allowance' => $this->marriage_allowance,
            'seniority_allowance' => $this->seniority_allowance,
            'insurance_included' => $this->insurance_included,
            'tax_included' => $this->tax_included,
            'created_at' => $this->created_at,
        ];
    }
}
