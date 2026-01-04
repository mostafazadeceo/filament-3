<?php

namespace Vendor\FilamentPayrollAttendanceIr\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Vendor\FilamentPayrollAttendanceIr\Domain\Models\HolidayRule;

class StoreHolidayRuleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', HolidayRule::class) ?? false;
    }

    public function rules(): array
    {
        return [
            'company_id' => ['required', 'integer'],
            'work_calendar_id' => ['required', 'integer'],
            'holiday_date' => ['required', 'date'],
            'title' => ['required', 'string', 'max:255'],
            'is_public' => ['nullable', 'boolean'],
            'source' => ['nullable', 'string', 'max:64'],
            'metadata' => ['nullable', 'array'],
        ];
    }
}
