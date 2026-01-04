<?php

namespace Vendor\FilamentPayrollAttendanceIr\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Vendor\FilamentPayrollAttendanceIr\Domain\Models\TimeEvent;

class StoreTimeEventRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', TimeEvent::class) ?? false;
    }

    public function rules(): array
    {
        return [
            'company_id' => ['required', 'integer'],
            'branch_id' => ['nullable', 'integer'],
            'employee_id' => ['required', 'integer'],
            'event_at' => ['nullable', 'date'],
            'event_type' => ['nullable', 'in:clock_in,clock_out,break_start,break_end'],
            'source' => ['nullable', 'string', 'max:32'],
            'device_ref' => ['nullable', 'string', 'max:128'],
            'latitude' => ['nullable', 'numeric', 'between:-90,90'],
            'longitude' => ['nullable', 'numeric', 'between:-180,180'],
            'wifi_ssid' => ['nullable', 'string', 'max:255'],
            'ip_address' => ['nullable', 'ip'],
            'proof_type' => ['nullable', 'string', 'max:64'],
            'proof_payload' => ['nullable', 'array'],
            'is_verified' => ['nullable', 'boolean'],
            'verified_by' => ['nullable', 'integer'],
            'verified_at' => ['nullable', 'date'],
            'metadata' => ['nullable', 'array'],
        ];
    }
}
