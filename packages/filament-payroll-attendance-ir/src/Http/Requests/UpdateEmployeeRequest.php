<?php

namespace Vendor\FilamentPayrollAttendanceIr\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateEmployeeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('update', $this->route('employee')) ?? false;
    }

    public function rules(): array
    {
        return [
            'company_id' => ['sometimes', 'integer'],
            'branch_id' => ['nullable', 'integer'],
            'user_id' => ['nullable', 'integer'],
            'employee_no' => ['nullable', 'string', 'max:64'],
            'first_name' => ['sometimes', 'string', 'max:255'],
            'last_name' => ['sometimes', 'string', 'max:255'],
            'national_id' => ['nullable', 'string', 'max:32'],
            'birth_date' => ['nullable', 'date'],
            'phone' => ['nullable', 'string', 'max:32'],
            'email' => ['nullable', 'email', 'max:255'],
            'marital_status' => ['nullable', 'in:single,married'],
            'children_count' => ['nullable', 'integer', 'min:0'],
            'employment_date' => ['nullable', 'date'],
            'job_title' => ['nullable', 'string', 'max:255'],
            'status' => ['nullable', 'in:active,inactive,terminated'],
            'bank_name' => ['nullable', 'string', 'max:255'],
            'bank_account' => ['nullable', 'string', 'max:64'],
            'bank_sheba' => ['nullable', 'string', 'max:32'],
        ];
    }
}
