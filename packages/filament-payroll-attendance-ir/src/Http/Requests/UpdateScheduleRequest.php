<?php

namespace Vendor\FilamentPayrollAttendanceIr\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateScheduleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('update', $this->route('schedule')) ?? false;
    }

    public function rules(): array
    {
        return [
            'company_id' => ['sometimes', 'integer'],
            'branch_id' => ['nullable', 'integer'],
            'employee_id' => ['sometimes', 'integer'],
            'shift_id' => ['nullable', 'integer'],
            'work_date' => ['sometimes', 'date'],
            'status' => ['nullable', 'in:scheduled,off,leave'],
            'notes' => ['nullable', 'string'],
        ];
    }
}
