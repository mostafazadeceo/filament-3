<?php

namespace Vendor\FilamentAccountingIr\Http\Requests;

use Illuminate\Validation\Rule;
use Vendor\FilamentAccountingIr\Models\AccountingBranch;

class UpdateAccountingBranchRequest extends BaseAccountingRequest
{
    public function authorize(): bool
    {
        $branch = $this->route('branch');

        return $branch instanceof AccountingBranch
            ? auth()->user()?->can('update', $branch)
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
            'code' => ['nullable', 'string', 'max:32'],
            'address' => ['nullable', 'string'],
            'postal_code' => ['nullable', 'string', 'max:32'],
            'phone' => ['nullable', 'string', 'max:32'],
            'is_active' => ['boolean'],
        ];
    }
}
