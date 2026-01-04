<?php

namespace Vendor\FilamentPayrollAttendanceIr\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateEmployeeConsentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('update', $this->route('employee_consent')) ?? false;
    }

    public function rules(): array
    {
        return [
            'company_id' => ['sometimes', 'integer'],
            'branch_id' => ['nullable', 'integer'],
            'employee_id' => ['sometimes', 'integer'],
            'consent_type' => ['sometimes', 'in:location_tracking,biometric_verification'],
            'is_granted' => ['nullable', 'boolean'],
            'granted_at' => ['nullable', 'date'],
            'revoked_at' => ['nullable', 'date'],
            'metadata' => ['nullable', 'array'],
        ];
    }
}
