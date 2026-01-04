<?php

namespace Vendor\FilamentPayrollAttendanceIr\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Vendor\FilamentPayrollAttendanceIr\Domain\Models\AttendancePolicy;

class StoreAttendancePolicyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', AttendancePolicy::class) ?? false;
    }

    public function rules(): array
    {
        return [
            'company_id' => ['required', 'integer'],
            'branch_id' => ['nullable', 'integer'],
            'name' => ['required', 'string', 'max:255'],
            'status' => ['nullable', 'in:active,inactive'],
            'is_default' => ['nullable', 'boolean'],
            'requires_consent' => ['nullable', 'boolean'],
            'allow_remote_work' => ['nullable', 'boolean'],
            'rules' => ['nullable', 'array'],
            'metadata' => ['nullable', 'array'],
        ];
    }
}
