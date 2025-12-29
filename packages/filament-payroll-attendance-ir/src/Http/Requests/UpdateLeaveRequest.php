<?php

namespace Vendor\FilamentPayrollAttendanceIr\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateLeaveRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('update', $this->route('leave_request')) ?? false;
    }

    public function rules(): array
    {
        return [
            'company_id' => ['sometimes', 'integer'],
            'branch_id' => ['nullable', 'integer'],
            'employee_id' => ['sometimes', 'integer'],
            'leave_type_id' => ['sometimes', 'integer'],
            'start_date' => ['sometimes', 'date'],
            'end_date' => ['sometimes', 'date'],
            'duration_hours' => ['nullable', 'numeric', 'min:0'],
            'status' => ['nullable', 'in:pending,approved,rejected,cancelled'],
            'notes' => ['nullable', 'string'],
        ];
    }
}
