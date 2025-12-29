<?php

namespace Vendor\FilamentPayrollAttendanceIr\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateLoanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('update', $this->route('loan')) ?? false;
    }

    public function rules(): array
    {
        return [
            'company_id' => ['sometimes', 'integer'],
            'branch_id' => ['nullable', 'integer'],
            'employee_id' => ['sometimes', 'integer'],
            'amount' => ['nullable', 'numeric', 'min:0'],
            'installment_count' => ['nullable', 'integer', 'min:1'],
            'installment_amount' => ['nullable', 'numeric', 'min:0'],
            'start_date' => ['nullable', 'date'],
            'status' => ['nullable', 'in:active,closed'],
        ];
    }
}
