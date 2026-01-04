<?php

namespace Vendor\FilamentPayrollAttendanceIr\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateOvertimeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('update', $this->route('overtime_request')) ?? false;
    }

    public function rules(): array
    {
        return [
            'company_id' => ['sometimes', 'integer'],
            'branch_id' => ['nullable', 'integer'],
            'employee_id' => ['sometimes', 'integer'],
            'work_date' => ['sometimes', 'date'],
            'requested_minutes' => ['sometimes', 'integer', 'min:0'],
            'status' => ['nullable', 'in:pending,approved,rejected,cancelled'],
            'reason' => ['nullable', 'string'],
            'metadata' => ['nullable', 'array'],
        ];
    }
}
