<?php

namespace Vendor\FilamentAccountingIr\Http\Requests;

use Illuminate\Validation\Rule;
use Vendor\FilamentAccountingIr\Models\FiscalPeriod;

class UpdateFiscalPeriodRequest extends BaseAccountingRequest
{
    public function authorize(): bool
    {
        $period = $this->route('fiscal_period');

        return $period instanceof FiscalPeriod
            ? auth()->user()?->can('update', $period)
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
            'fiscal_year_id' => [
                'sometimes',
                'integer',
                Rule::exists('accounting_ir_fiscal_years', 'id')
                    ->when($tenantId, fn ($rule) => $rule->where('tenant_id', $tenantId)),
            ],
            'name' => ['sometimes', 'string', 'max:255'],
            'start_date' => ['sometimes', 'date'],
            'end_date' => ['sometimes', 'date', 'after_or_equal:start_date'],
            'period_type' => ['nullable', 'string', 'max:32'],
            'is_closed' => ['boolean'],
        ];
    }
}
