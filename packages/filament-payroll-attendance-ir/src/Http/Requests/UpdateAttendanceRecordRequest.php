<?php

namespace Vendor\FilamentPayrollAttendanceIr\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAttendanceRecordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('update', $this->route('attendance_record')) ?? false;
    }

    public function rules(): array
    {
        return [
            'company_id' => ['sometimes', 'integer'],
            'branch_id' => ['nullable', 'integer'],
            'employee_id' => ['sometimes', 'integer'],
            'shift_id' => ['nullable', 'integer'],
            'work_date' => ['sometimes', 'date'],
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
