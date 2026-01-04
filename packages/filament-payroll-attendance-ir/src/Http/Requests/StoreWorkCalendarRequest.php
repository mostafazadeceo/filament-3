<?php

namespace Vendor\FilamentPayrollAttendanceIr\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Vendor\FilamentPayrollAttendanceIr\Domain\Models\WorkCalendar;

class StoreWorkCalendarRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', WorkCalendar::class) ?? false;
    }

    public function rules(): array
    {
        return [
            'company_id' => ['required', 'integer'],
            'branch_id' => ['nullable', 'integer'],
            'name' => ['required', 'string', 'max:255'],
            'calendar_type' => ['nullable', 'in:jalali,gregorian'],
            'timezone' => ['nullable', 'string', 'max:64'],
            'is_default' => ['nullable', 'boolean'],
            'metadata' => ['nullable', 'array'],
        ];
    }
}
