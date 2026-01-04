<?php

namespace Vendor\FilamentPayrollAttendanceIr\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Vendor\FilamentPayrollAttendanceIr\Domain\Models\EmployeeConsent;

class StoreEmployeeConsentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', EmployeeConsent::class) ?? false;
    }

    public function rules(): array
    {
        return [
            'company_id' => ['required', 'integer'],
            'branch_id' => ['nullable', 'integer'],
            'employee_id' => ['required', 'integer'],
            'consent_type' => ['required', 'in:location_tracking,biometric_verification'],
            'is_granted' => ['nullable', 'boolean'],
            'granted_at' => ['nullable', 'date'],
            'revoked_at' => ['nullable', 'date'],
            'metadata' => ['nullable', 'array'],
        ];
    }
}
