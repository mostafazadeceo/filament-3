<?php

namespace Vendor\FilamentAccountingIr\Http\Requests;

use Illuminate\Validation\Rule;
use Vendor\FilamentAccountingIr\Models\Employee;

class UpdateEmployeeRequest extends BaseAccountingRequest
{
    public function authorize(): bool
    {
        $employee = $this->route('employee');

        return $employee instanceof Employee
            ? auth()->user()?->can('update', $employee)
            : false;
    }

    public function rules(): array
    {
        $tenantId = $this->tenantId();

        return [
            'company_id' => [
                'sometimes',
                'integer',
                Rule::exists('accounting_ir_companies', 'id')
                    ->when($tenantId, fn ($rule) => $rule->where('tenant_id', $tenantId)),
            ],
            'branch_id' => [
                'nullable',
                'integer',
                Rule::exists('accounting_ir_branches', 'id')
                    ->when($tenantId, fn ($rule) => $rule->where('tenant_id', $tenantId)),
            ],
            'employee_no' => ['nullable', 'string', 'max:64'],
            'name' => ['sometimes', 'string', 'max:255'],
            'national_id' => ['nullable', 'string', 'max:32'],
            'hire_date' => ['nullable', 'date'],
            'status' => ['nullable', 'string', 'max:32'],
        ];
    }
}
