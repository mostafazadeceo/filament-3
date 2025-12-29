<?php

namespace Vendor\FilamentPayrollAttendanceIr\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Vendor\FilamentPayrollAttendanceIr\Models\PayrollContract;

class StoreContractRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', PayrollContract::class) ?? false;
    }

    public function rules(): array
    {
        return [
            'company_id' => ['required', 'integer'],
            'branch_id' => ['nullable', 'integer'],
            'employee_id' => ['required', 'integer'],
            'scope' => ['required', 'in:official,internal'],
            'status' => ['nullable', 'in:active,inactive'],
            'effective_from' => ['required', 'date'],
            'effective_to' => ['nullable', 'date'],
            'base_salary' => ['nullable', 'numeric', 'min:0'],
            'daily_hours' => ['nullable', 'numeric', 'min:0'],
            'weekly_hours' => ['nullable', 'numeric', 'min:0'],
            'monthly_hours' => ['nullable', 'numeric', 'min:0'],
            'housing_allowance' => ['nullable', 'numeric', 'min:0'],
            'food_allowance' => ['nullable', 'numeric', 'min:0'],
            'child_allowance' => ['nullable', 'numeric', 'min:0'],
            'marriage_allowance' => ['nullable', 'numeric', 'min:0'],
            'seniority_allowance' => ['nullable', 'numeric', 'min:0'],
            'insurance_included' => ['nullable', 'boolean'],
            'tax_included' => ['nullable', 'boolean'],
        ];
    }
}
