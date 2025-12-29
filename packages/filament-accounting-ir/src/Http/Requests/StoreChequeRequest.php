<?php

namespace Vendor\FilamentAccountingIr\Http\Requests;

use Illuminate\Validation\Rule;
use Vendor\FilamentAccountingIr\Models\Cheque;

class StoreChequeRequest extends BaseAccountingRequest
{
    public function authorize(): bool
    {
        return auth()->user()?->can('create', Cheque::class) ?? false;
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
            'party_id' => [
                'nullable',
                'integer',
                Rule::exists('accounting_ir_parties', 'id')
                    ->when($tenantId, fn ($rule) => $rule->where('tenant_id', $tenantId)),
            ],
            'treasury_account_id' => [
                'nullable',
                'integer',
                Rule::exists('accounting_ir_treasury_accounts', 'id')
                    ->when($tenantId, fn ($rule) => $rule->where('tenant_id', $tenantId)),
            ],
            'direction' => ['nullable', 'string', 'max:32'],
            'cheque_no' => ['nullable', 'string', 'max:64'],
            'bank_name' => ['nullable', 'string', 'max:255'],
            'branch_name' => ['nullable', 'string', 'max:255'],
            'due_date' => ['nullable', 'date'],
            'amount' => ['nullable', 'numeric', 'min:0'],
            'status' => ['nullable', 'string', 'max:32'],
            'metadata' => ['nullable', 'array'],
        ];
    }
}
