<?php

namespace Haida\FilamentPettyCashIr\Http\Requests;

use Haida\FilamentPettyCashIr\Models\PettyCashFund;
use Illuminate\Validation\Rule;

class UpdateFundRequest extends BasePettyCashRequest
{
    public function authorize(): bool
    {
        $fund = $this->route('fund');

        return auth()->user()?->can('update', $fund ?? PettyCashFund::class) ?? false;
    }

    public function rules(): array
    {
        $tenantId = $this->tenantId();

        return [
            'tenant_id' => ['nullable', 'integer'],
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
            'custodian_user_id' => ['nullable', 'integer', Rule::exists('users', 'id')],
            'accounting_cash_account_id' => [
                'nullable',
                'integer',
                Rule::exists('accounting_ir_chart_accounts', 'id')
                    ->when($tenantId, fn ($rule) => $rule->where('tenant_id', $tenantId)),
            ],
            'accounting_source_account_id' => [
                'nullable',
                'integer',
                Rule::exists('accounting_ir_chart_accounts', 'id')
                    ->when($tenantId, fn ($rule) => $rule->where('tenant_id', $tenantId)),
            ],
            'default_expense_account_id' => [
                'nullable',
                'integer',
                Rule::exists('accounting_ir_chart_accounts', 'id')
                    ->when($tenantId, fn ($rule) => $rule->where('tenant_id', $tenantId)),
            ],
            'accounting_treasury_account_id' => [
                'nullable',
                'integer',
                Rule::exists('accounting_ir_treasury_accounts', 'id')
                    ->when($tenantId, fn ($rule) => $rule->where('tenant_id', $tenantId)),
            ],
            'name' => ['sometimes', 'string', 'max:255'],
            'code' => ['nullable', 'string', 'max:64'],
            'status' => ['nullable', 'string', 'max:32'],
            'currency' => ['nullable', 'string', 'max:10'],
            'opening_balance' => ['nullable', 'numeric', 'min:0'],
            'current_balance' => ['nullable', 'numeric', 'min:0'],
            'threshold_balance' => ['nullable', 'numeric', 'min:0'],
            'replenishment_amount' => ['nullable', 'numeric', 'min:0'],
            'metadata' => ['nullable', 'array'],
        ];
    }
}
