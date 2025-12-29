<?php

namespace Vendor\FilamentPayrollAttendanceIr\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class EmployeeResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'company_id' => $this->company_id,
            'branch_id' => $this->branch_id,
            'employee_no' => $this->employee_no,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'national_id' => $this->national_id,
            'birth_date' => $this->birth_date,
            'phone' => $this->phone,
            'email' => $this->email,
            'marital_status' => $this->marital_status,
            'children_count' => $this->children_count,
            'employment_date' => $this->employment_date,
            'job_title' => $this->job_title,
            'status' => $this->status,
            'bank_name' => $this->bank_name,
            'bank_account' => $this->bank_account,
            'bank_sheba' => $this->bank_sheba,
            'created_at' => $this->created_at,
        ];
    }
}
