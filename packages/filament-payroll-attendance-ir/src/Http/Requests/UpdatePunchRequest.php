<?php

namespace Vendor\FilamentPayrollAttendanceIr\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePunchRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('update', $this->route('punch')) ?? false;
    }

    public function rules(): array
    {
        return [
            'company_id' => ['sometimes', 'integer'],
            'branch_id' => ['nullable', 'integer'],
            'employee_id' => ['sometimes', 'integer'],
            'punch_at' => ['sometimes', 'date'],
            'type' => ['sometimes', 'in:in,out'],
            'source' => ['nullable', 'in:device,web,bot,manual'],
            'device_ref' => ['nullable', 'string', 'max:255'],
            'latitude' => ['nullable', 'numeric'],
            'longitude' => ['nullable', 'numeric'],
        ];
    }
}
