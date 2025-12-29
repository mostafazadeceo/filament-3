<?php

namespace Vendor\FilamentAccountingIr\Http\Requests;

use Illuminate\Validation\Rule;
use Vendor\FilamentAccountingIr\Models\FiscalYear;

class UpdateFiscalYearRequest extends BaseAccountingRequest
{
    public function authorize(): bool
    {
        $year = $this->route('fiscal_year');

        return $year instanceof FiscalYear
            ? auth()->user()?->can('update', $year)
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
            'name' => ['sometimes', 'string', 'max:255'],
            'start_date' => ['sometimes', 'date'],
            'end_date' => ['sometimes', 'date', 'after_or_equal:start_date'],
            'is_closed' => ['boolean'],
        ];
    }
}
