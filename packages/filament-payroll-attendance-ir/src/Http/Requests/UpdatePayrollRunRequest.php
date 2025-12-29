<?php

namespace Vendor\FilamentPayrollAttendanceIr\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePayrollRunRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('update', $this->route('payroll_run')) ?? false;
    }

    public function rules(): array
    {
        return [
            'company_id' => ['sometimes', 'integer'],
            'branch_id' => ['nullable', 'integer'],
            'period_start' => ['sometimes', 'date'],
            'period_end' => ['sometimes', 'date'],
            'notes' => ['nullable', 'string'],
            'status' => ['nullable', 'in:draft,approved,posted,locked'],
        ];
    }
}
