<?php

namespace Vendor\FilamentPayrollAttendanceIr\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Vendor\FilamentPayrollAttendanceIr\Models\PayrollLoan;

class StoreLoanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', PayrollLoan::class) ?? false;
    }

    public function rules(): array
    {
        return [
            'company_id' => ['required', 'integer'],
            'branch_id' => ['nullable', 'integer'],
            'employee_id' => ['required', 'integer'],
            'amount' => ['required', 'numeric', 'min:0'],
            'installment_count' => ['required', 'integer', 'min:1'],
            'installment_amount' => ['nullable', 'numeric', 'min:0'],
            'start_date' => ['nullable', 'date'],
            'status' => ['nullable', 'in:active,closed'],
        ];
    }
}
