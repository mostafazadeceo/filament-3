<?php

namespace Vendor\FilamentPayrollAttendanceIr\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Vendor\FilamentPayrollAttendanceIr\Domain\Models\OvertimeRequest;

class StoreOvertimeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', OvertimeRequest::class) ?? false;
    }

    public function rules(): array
    {
        return [
            'company_id' => ['required', 'integer'],
            'branch_id' => ['nullable', 'integer'],
            'employee_id' => ['required', 'integer'],
            'work_date' => ['required', 'date'],
            'requested_minutes' => ['required', 'integer', 'min:0'],
            'reason' => ['nullable', 'string'],
            'metadata' => ['nullable', 'array'],
        ];
    }
}
