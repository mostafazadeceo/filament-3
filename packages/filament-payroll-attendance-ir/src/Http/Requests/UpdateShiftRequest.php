<?php

namespace Vendor\FilamentPayrollAttendanceIr\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateShiftRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('update', $this->route('shift')) ?? false;
    }

    public function rules(): array
    {
        return [
            'company_id' => ['sometimes', 'integer'],
            'branch_id' => ['nullable', 'integer'],
            'name' => ['sometimes', 'string', 'max:255'],
            'code' => ['nullable', 'string', 'max:64'],
            'start_time' => ['sometimes'],
            'end_time' => ['sometimes'],
            'break_minutes' => ['nullable', 'integer', 'min:0'],
            'is_night' => ['nullable', 'boolean'],
            'is_rotating' => ['nullable', 'boolean'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }
}
