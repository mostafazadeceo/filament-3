<?php

namespace Vendor\FilamentPayrollAttendanceIr\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAttendancePolicyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('update', $this->route('attendance_policy')) ?? false;
    }

    public function rules(): array
    {
        return [
            'company_id' => ['sometimes', 'integer'],
            'branch_id' => ['nullable', 'integer'],
            'name' => ['sometimes', 'string', 'max:255'],
            'status' => ['nullable', 'in:active,inactive'],
            'is_default' => ['nullable', 'boolean'],
            'requires_consent' => ['nullable', 'boolean'],
            'allow_remote_work' => ['nullable', 'boolean'],
            'rules' => ['nullable', 'array'],
            'metadata' => ['nullable', 'array'],
        ];
    }
}
