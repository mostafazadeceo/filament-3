<?php

namespace Vendor\FilamentPayrollAttendanceIr\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Vendor\FilamentPayrollAttendanceIr\Models\PayrollAdvance;

class StoreAdvanceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', PayrollAdvance::class) ?? false;
    }

    public function rules(): array
    {
        return [
            'company_id' => ['required', 'integer'],
            'branch_id' => ['nullable', 'integer'],
            'employee_id' => ['required', 'integer'],
            'amount' => ['required', 'numeric', 'min:0'],
            'advance_date' => ['nullable', 'date'],
            'status' => ['nullable', 'in:open,settled'],
        ];
    }
}
