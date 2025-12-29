<?php

namespace Vendor\FilamentPayrollAttendanceIr\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Vendor\FilamentPayrollAttendanceIr\Models\PayrollTimePunch;

class StorePunchRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', PayrollTimePunch::class) ?? false;
    }

    public function rules(): array
    {
        return [
            'company_id' => ['required', 'integer'],
            'branch_id' => ['nullable', 'integer'],
            'employee_id' => ['required', 'integer'],
            'punch_at' => ['required', 'date'],
            'type' => ['required', 'in:in,out'],
            'source' => ['nullable', 'in:device,web,bot,manual'],
            'device_ref' => ['nullable', 'string', 'max:255'],
            'latitude' => ['nullable', 'numeric'],
            'longitude' => ['nullable', 'numeric'],
        ];
    }
}
