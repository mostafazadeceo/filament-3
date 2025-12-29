<?php

namespace Vendor\FilamentAccountingIr\Http\Requests;

use Filamat\IamSuite\Support\IamAuthorization;
use Illuminate\Validation\Rule;

class GeneralLedgerReportRequest extends BaseAccountingRequest
{
    public function authorize(): bool
    {
        return IamAuthorization::allows('accounting.report.view');
    }

    public function rules(): array
    {
        $tenantId = $this->tenantId();

        return [
            'company_id' => [
                'required',
                'integer',
                Rule::exists('accounting_ir_companies', 'id')
                    ->when($tenantId, fn ($rule) => $rule->where('tenant_id', $tenantId)),
            ],
            'account_id' => [
                'required',
                'integer',
                Rule::exists('accounting_ir_chart_accounts', 'id')
                    ->where('is_postable', true)
                    ->when($tenantId, fn ($rule) => $rule->where('tenant_id', $tenantId)),
            ],
            'fiscal_year_id' => [
                'nullable',
                'integer',
                Rule::exists('accounting_ir_fiscal_years', 'id')
                    ->when($tenantId, fn ($rule) => $rule->where('tenant_id', $tenantId)),
            ],
            'branch_id' => [
                'nullable',
                'integer',
                Rule::exists('accounting_ir_branches', 'id')
                    ->when($tenantId, fn ($rule) => $rule->where('tenant_id', $tenantId)),
            ],
            'from' => ['nullable', 'date', 'before_or_equal:to'],
            'to' => ['nullable', 'date', 'after_or_equal:from'],
        ];
    }
}
