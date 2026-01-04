<?php

namespace Vendor\FilamentPayrollAttendanceIr\Http\Requests;

use Filamat\IamSuite\Support\IamAuthorization;
use Illuminate\Foundation\Http\FormRequest;

class ReportFiltersRequest extends FormRequest
{
    public function authorize(): bool
    {
        return IamAuthorization::allows('payroll.report.view');
    }

    public function rules(): array
    {
        return [
            'company_id' => ['required', 'integer'],
            'branch_id' => ['nullable', 'integer'],
            'start' => ['nullable', 'date'],
            'end' => ['nullable', 'date'],
        ];
    }
}
