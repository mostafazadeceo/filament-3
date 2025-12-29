<?php

namespace Vendor\FilamentAccountingIr\Http\Requests;

use Illuminate\Validation\Rule;

class UpdatePayrollRunRequest extends BaseAccountingRequest
{
    public function authorize(): bool
    {
        return auth()->user()?->can('update', $this->route('payroll_run')) ?? false;
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
            'fiscal_period_id' => [
                'nullable',
                'integer',
                Rule::exists('accounting_ir_fiscal_periods', 'id')
                    ->when($tenantId, fn ($rule) => $rule->where('tenant_id', $tenantId)),
            ],
            'run_date' => ['nullable', 'date'],
            'status' => ['nullable', 'string', 'max:32'],
            'metadata' => ['nullable', 'array'],
        ];
    }
}
