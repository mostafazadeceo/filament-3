<?php

namespace Haida\FilamentPettyCashIr\Http\Requests;

use Haida\FilamentPettyCashIr\Models\PettyCashReplenishment;
use Illuminate\Validation\Rule;

class UpdateReplenishmentRequest extends BasePettyCashRequest
{
    public function authorize(): bool
    {
        $replenishment = $this->route('replenishment');

        return auth()->user()?->can('update', $replenishment ?? PettyCashReplenishment::class) ?? false;
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
            'fund_id' => [
                'sometimes',
                'integer',
                Rule::exists('petty_cash_funds', 'id')
                    ->when($tenantId, fn ($rule) => $rule->where('tenant_id', $tenantId)),
            ],
            'requested_by' => ['nullable', 'integer', Rule::exists('users', 'id')],
            'source_treasury_account_id' => [
                'nullable',
                'integer',
                Rule::exists('accounting_ir_treasury_accounts', 'id')
                    ->when($tenantId, fn ($rule) => $rule->where('tenant_id', $tenantId)),
            ],
            'request_date' => ['sometimes', 'date'],
            'amount' => ['sometimes', 'numeric', 'min:0.01'],
            'currency' => ['nullable', 'string', 'max:10'],
            'status' => ['nullable', 'string', 'max:32'],
            'description' => ['nullable', 'string'],
            'metadata' => ['nullable', 'array'],
        ];
    }
}
