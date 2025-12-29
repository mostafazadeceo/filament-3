<?php

namespace Vendor\FilamentPayrollAttendanceIr\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Vendor\FilamentPayrollAttendanceIr\Models\PayrollLeaveRequest;

class StoreLeaveRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', PayrollLeaveRequest::class) ?? false;
    }

    public function rules(): array
    {
        return [
            'company_id' => ['required', 'integer'],
            'branch_id' => ['nullable', 'integer'],
            'employee_id' => ['required', 'integer'],
            'leave_type_id' => ['required', 'integer'],
            'start_date' => ['required', 'date'],
            'end_date' => ['required', 'date'],
            'duration_hours' => ['nullable', 'numeric', 'min:0'],
            'notes' => ['nullable', 'string'],
        ];
    }
}
