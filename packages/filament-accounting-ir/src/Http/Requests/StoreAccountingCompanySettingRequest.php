<?php

namespace Vendor\FilamentAccountingIr\Http\Requests;

use Illuminate\Validation\Rule;
use Vendor\FilamentAccountingIr\Models\AccountingCompanySetting;

class StoreAccountingCompanySettingRequest extends BaseAccountingRequest
{
    public function authorize(): bool
    {
        return auth()->user()?->can('create', AccountingCompanySetting::class) ?? false;
    }

    public function rules(): array
    {
        $tenantId = $this->tenantId();
        $companyId = $this->input('company_id');

        $companyRule = Rule::exists('accounting_ir_companies', 'id')
            ->when($tenantId, fn ($rule) => $rule->where('tenant_id', $tenantId));

        $uniqueRule = Rule::unique('accounting_ir_company_settings', 'company_id')
            ->when($tenantId, fn ($rule) => $rule->where('tenant_id', $tenantId));

        $accountRule = $this->accountRule($companyId);

        return [
            'tenant_id' => ['nullable', 'integer'],
            'company_id' => ['required', 'integer', $companyRule, $uniqueRule],
            'posting_accounts' => ['required', 'array'],
            'posting_accounts.sales_revenue' => ['required', 'integer', $accountRule],
            'posting_accounts.accounts_receivable' => ['required', 'integer', $accountRule],
            'posting_accounts.sales_tax' => ['nullable', 'integer', $accountRule],
            'posting_accounts.purchase_expense' => ['required', 'integer', $accountRule],
            'posting_accounts.accounts_payable' => ['required', 'integer', $accountRule],
            'posting_accounts.purchase_tax' => ['nullable', 'integer', $accountRule],
            'posting_accounts.cash' => ['nullable', 'integer', $accountRule],
            'posting_accounts.bank' => ['nullable', 'integer', $accountRule],
            'posting_requires_approval' => ['boolean'],
            'allow_negative_inventory' => ['boolean'],
        ];
    }

    protected function accountRule(?int $companyId): Rule
    {
        $rule = Rule::exists('accounting_ir_chart_accounts', 'id')
            ->where('is_postable', true);

        if ($companyId) {
            $rule->where('company_id', $companyId);
        }

        return $rule;
    }
}
