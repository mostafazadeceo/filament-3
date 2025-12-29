<?php

namespace Vendor\FilamentAccountingIr\Http\Requests;

use Illuminate\Validation\Rule;
use Vendor\FilamentAccountingIr\Models\InventoryWarehouse;

class UpdateInventoryWarehouseRequest extends BaseAccountingRequest
{
    public function authorize(): bool
    {
        $warehouse = $this->route('inventory_warehouse');

        return $warehouse instanceof InventoryWarehouse
            ? auth()->user()?->can('update', $warehouse)
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
            'branch_id' => [
                'nullable',
                'integer',
                Rule::exists('accounting_ir_branches', 'id')
                    ->when($tenantId, fn ($rule) => $rule->where('tenant_id', $tenantId)),
            ],
            'name' => ['sometimes', 'string', 'max:255'],
            'code' => ['nullable', 'string', 'max:64'],
            'is_active' => ['boolean'],
        ];
    }
}
