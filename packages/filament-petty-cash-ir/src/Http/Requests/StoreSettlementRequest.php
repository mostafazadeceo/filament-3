<?php

namespace Haida\FilamentPettyCashIr\Http\Requests;

use Haida\FilamentPettyCashIr\Models\PettyCashSettlement;
use Illuminate\Validation\Rule;

class StoreSettlementRequest extends BasePettyCashRequest
{
    public function authorize(): bool
    {
        return auth()->user()?->can('create', PettyCashSettlement::class) ?? false;
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
            'requested_by' => ['nullable', 'integer', Rule::exists('users', 'id')],
            'period_start' => ['required', 'date'],
            'period_end' => ['required', 'date', 'after_or_equal:period_start'],
            'status' => ['nullable', 'string', 'max:32'],
            'notes' => ['nullable', 'string'],
            'metadata' => ['nullable', 'array'],
            'expense_ids' => ['nullable', 'array'],
            'expense_ids.*' => [
                'integer',
                Rule::exists('petty_cash_expenses', 'id')
                    ->when($tenantId, fn ($rule) => $rule->where('tenant_id', $tenantId)),
            ],
        ];
    }
}
