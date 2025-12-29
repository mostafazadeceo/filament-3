<?php

namespace Vendor\FilamentAccountingIr\Http\Requests;

use Illuminate\Validation\Rule;
use Vendor\FilamentAccountingIr\Models\FixedAsset;

class StoreFixedAssetRequest extends BaseAccountingRequest
{
    public function authorize(): bool
    {
        return auth()->user()?->can('create', FixedAsset::class) ?? false;
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
            'name' => ['required', 'string', 'max:255'],
            'asset_code' => ['nullable', 'string', 'max:64'],
            'category' => ['nullable', 'string', 'max:255'],
            'acquisition_date' => ['nullable', 'date'],
            'cost' => ['nullable', 'numeric', 'min:0'],
            'salvage_value' => ['nullable', 'numeric', 'min:0'],
            'depreciation_method' => ['nullable', 'string', 'max:64'],
            'useful_life_months' => ['nullable', 'integer', 'min:1'],
            'status' => ['nullable', 'string', 'max:32'],
        ];
    }
}
