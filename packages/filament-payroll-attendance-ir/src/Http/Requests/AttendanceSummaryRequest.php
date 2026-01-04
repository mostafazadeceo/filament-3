<?php

namespace Vendor\FilamentPayrollAttendanceIr\Http\Requests;

use Filamat\IamSuite\Support\IamAuthorization;
use Illuminate\Foundation\Http\FormRequest;

class AttendanceSummaryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return IamAuthorization::allows('payroll.report.view');
    }

    public function rules(): array
    {
        return [
            'employee_id' => ['required', 'integer'],
            'start' => ['nullable', 'date'],
            'end' => ['nullable', 'date'],
        ];
    }
}
