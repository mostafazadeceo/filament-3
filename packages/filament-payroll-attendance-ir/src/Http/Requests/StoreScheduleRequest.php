<?php

namespace Vendor\FilamentPayrollAttendanceIr\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Vendor\FilamentPayrollAttendanceIr\Models\PayrollAttendanceSchedule;

class StoreScheduleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', PayrollAttendanceSchedule::class) ?? false;
    }

    public function rules(): array
    {
        return [
            'company_id' => ['required', 'integer'],
            'branch_id' => ['nullable', 'integer'],
            'employee_id' => ['required', 'integer'],
            'shift_id' => ['nullable', 'integer'],
            'work_date' => ['required', 'date'],
            'status' => ['nullable', 'in:scheduled,off,leave'],
            'notes' => ['nullable', 'string'],
        ];
    }
}
