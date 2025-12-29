<?php

namespace Vendor\FilamentPayrollAttendanceIr\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Vendor\FilamentPayrollAttendanceIr\Models\PayrollAttendanceRecord;

class StoreAttendanceRecordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', PayrollAttendanceRecord::class) ?? false;
    }

    public function rules(): array
    {
        return [
            'company_id' => ['required', 'integer'],
            'branch_id' => ['nullable', 'integer'],
            'employee_id' => ['required', 'integer'],
            'shift_id' => ['nullable', 'integer'],
            'work_date' => ['required', 'date'],
            'scheduled_in' => ['nullable', 'date'],
            'scheduled_out' => ['nullable', 'date'],
            'actual_in' => ['nullable', 'date'],
            'actual_out' => ['nullable', 'date'],
            'worked_minutes' => ['nullable', 'integer', 'min:0'],
            'late_minutes' => ['nullable', 'integer', 'min:0'],
            'early_leave_minutes' => ['nullable', 'integer', 'min:0'],
            'overtime_minutes' => ['nullable', 'integer', 'min:0'],
            'night_minutes' => ['nullable', 'integer', 'min:0'],
            'friday_minutes' => ['nullable', 'integer', 'min:0'],
            'holiday_minutes' => ['nullable', 'integer', 'min:0'],
            'absence_minutes' => ['nullable', 'integer', 'min:0'],
            'status' => ['nullable', 'in:draft,approved,locked'],
        ];
    }
}
