<?php

namespace Vendor\FilamentAccountingIr\Http\Requests;

use Illuminate\Validation\Rule;
use Vendor\FilamentAccountingIr\Models\AccountingCompanySetting;

class UpdateAccountingCompanySettingRequest extends BaseAccountingRequest
{
    public function authorize(): bool
    {
        $setting = $this->route('company_setting');

        return $setting instanceof AccountingCompanySetting
            ? auth()->user()?->can('update', $setting)
            : false;
    }

    public function rules(): array
    {
        $tenantId = $this->tenantId();
        $setting = $this->route('company_setting');
        $companyId = $this->input('company_id') ?? ($setting?->company_id);

        $companyRule = Rule::exists('accounting_ir_companies', 'id')
            ->when($tenantId, fn ($rule) => $rule->where('tenant_id', $tenantId));

        $uniqueRule = Rule::unique('accounting_ir_company_settings', 'company_id')
            ->when($tenantId, fn ($rule) => $rule->where('tenant_id', $tenantId));

        if ($setting) {
            $uniqueRule->ignore($setting->getKey());
        }

        $accountRule = $this->accountRule($companyId);

        return [
            'tenant_id' => ['nullable', 'integer'],
            'company_id' => ['sometimes', 'integer', $companyRule, $uniqueRule],
            'posting_accounts' => ['sometimes', 'array'],
            'posting_accounts.sales_revenue' => ['sometimes', 'nullable', 'integer', $accountRule],
            'posting_accounts.accounts_receivable' => ['sometimes', 'nullable', 'integer', $accountRule],
            'posting_accounts.sales_tax' => ['sometimes', 'nullable', 'integer', $accountRule],
            'posting_accounts.purchase_expense' => ['sometimes', 'nullable', 'integer', $accountRule],
            'posting_accounts.accounts_payable' => ['sometimes', 'nullable', 'integer', $accountRule],
            'posting_accounts.purchase_tax' => ['sometimes', 'nullable', 'integer', $accountRule],
            'posting_accounts.cash' => ['sometimes', 'nullable', 'integer', $accountRule],
            'posting_accounts.bank' => ['sometimes', 'nullable', 'integer', $accountRule],
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
