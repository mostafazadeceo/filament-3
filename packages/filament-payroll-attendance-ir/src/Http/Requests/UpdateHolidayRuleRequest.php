<?php

namespace Vendor\FilamentPayrollAttendanceIr\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateHolidayRuleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('update', $this->route('holiday_rule')) ?? false;
    }

    public function rules(): array
    {
        return [
            'company_id' => ['sometimes', 'integer'],
            'work_calendar_id' => ['sometimes', 'integer'],
            'holiday_date' => ['sometimes', 'date'],
            'title' => ['sometimes', 'string', 'max:255'],
            'is_public' => ['nullable', 'boolean'],
            'source' => ['nullable', 'string', 'max:64'],
            'metadata' => ['nullable', 'array'],
        ];
    }
}
