<?php

namespace Vendor\FilamentPayrollAttendanceIr\Http\Requests;

use Filamat\IamSuite\Support\IamAuthorization;
use Illuminate\Foundation\Http\FormRequest;

class AiManagerReportRequest extends FormRequest
{
    public function authorize(): bool
    {
        return IamAuthorization::allows('payroll.ai.use');
    }

    public function rules(): array
    {
        return [
            'company_id' => ['required', 'integer'],
            'branch_id' => ['nullable', 'integer'],
            'period_start' => ['nullable', 'date'],
            'period_end' => ['nullable', 'date'],
        ];
    }
}
