<?php

namespace Vendor\FilamentAccountingIr\Http\Requests;

use Illuminate\Validation\Rule;
use Vendor\FilamentAccountingIr\Models\TreasuryAccount;

class StoreTreasuryAccountRequest extends BaseAccountingRequest
{
    public function authorize(): bool
    {
        return auth()->user()?->can('create', TreasuryAccount::class) ?? false;
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
            'account_type' => ['required', 'string', 'max:32'],
            'name' => ['required', 'string', 'max:255'],
            'account_no' => ['nullable', 'string', 'max:64'],
            'iban' => ['nullable', 'string', 'max:64'],
            'bank_name' => ['nullable', 'string', 'max:255'],
            'currency' => ['nullable', 'string', 'max:8'],
            'is_active' => ['boolean'],
        ];
    }
}
