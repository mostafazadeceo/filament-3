<?php

namespace Vendor\FilamentPayrollAttendanceIr\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateWorkCalendarRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('update', $this->route('work_calendar')) ?? false;
    }

    public function rules(): array
    {
        return [
            'company_id' => ['sometimes', 'integer'],
            'branch_id' => ['nullable', 'integer'],
            'name' => ['sometimes', 'string', 'max:255'],
            'calendar_type' => ['nullable', 'in:jalali,gregorian'],
            'timezone' => ['nullable', 'string', 'max:64'],
            'is_default' => ['nullable', 'boolean'],
            'metadata' => ['nullable', 'array'],
        ];
    }
}
