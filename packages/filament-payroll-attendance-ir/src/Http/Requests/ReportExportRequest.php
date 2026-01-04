<?php

namespace Vendor\FilamentPayrollAttendanceIr\Http\Requests;

use Filamat\IamSuite\Support\IamAuthorization;
use Illuminate\Foundation\Http\FormRequest;

class ReportExportRequest extends FormRequest
{
    public function authorize(): bool
    {
        return IamAuthorization::allows('payroll.report.export');
    }

    public function rules(): array
    {
        return [
            'type' => ['required', 'string', 'in:timesheets,timesheet_summary,tardiness,overtime,leave_balance,coverage_gaps,attendance_summary'],
            'filters' => ['nullable', 'array'],
            'filters.company_id' => ['nullable', 'integer'],
            'filters.branch_id' => ['nullable', 'integer'],
            'filters.employee_id' => ['nullable', 'integer'],
            'filters.period_start' => ['nullable', 'date'],
            'filters.period_end' => ['nullable', 'date'],
            'filters.start' => ['nullable', 'date'],
            'filters.end' => ['nullable', 'date'],
        ];
    }
}
