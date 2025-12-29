<?php

namespace Haida\FilamentPettyCashIr\Http\Requests;

use Haida\FilamentPettyCashIr\Models\PettyCashExpense;
use Illuminate\Validation\Rule;

class StoreExpenseRequest extends BasePettyCashRequest
{
    public function authorize(): bool
    {
        return auth()->user()?->can('create', PettyCashExpense::class) ?? false;
    }

    public function rules(): array
    {
        $tenantId = $this->tenantId();

        return [
            'tenant_id' => ['nullable', 'integer'],
            'company_id' => [
                'required',
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
            'fund_id' => [
                'required',
                'integer',
                Rule::exists('petty_cash_funds', 'id')
                    ->when($tenantId, fn ($rule) => $rule->where('tenant_id', $tenantId)),
            ],
            'category_id' => [
                'nullable',
                'integer',
                Rule::exists('petty_cash_categories', 'id')
                    ->when($tenantId, fn ($rule) => $rule->where('tenant_id', $tenantId)),
            ],
            'requested_by' => ['nullable', 'integer', Rule::exists('users', 'id')],
            'accounting_party_id' => [
                'nullable',
                'integer',
                Rule::exists('accounting_ir_parties', 'id')
                    ->when($tenantId, fn ($rule) => $rule->where('tenant_id', $tenantId)),
            ],
            'expense_date' => ['required', 'date'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'currency' => ['nullable', 'string', 'max:10'],
            'status' => ['nullable', 'string', 'max:32'],
            'reference' => ['nullable', 'string', 'max:64'],
            'payee_name' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'receipt_required' => ['nullable', 'boolean'],
            'metadata' => ['nullable', 'array'],
        ];
    }
}
