<?php

namespace Haida\FilamentRestaurantOps\Http\Requests;

use Haida\FilamentRestaurantOps\Models\RestaurantInventoryDoc;
use Illuminate\Validation\Rule;

class UpdateInventoryDocRequest extends BaseRestaurantOpsRequest
{
    public function authorize(): bool
    {
        $doc = $this->route('inventory_doc');

        return $doc
            ? auth()->user()?->can('update', $doc) ?? false
            : auth()->user()?->can('update', RestaurantInventoryDoc::class) ?? false;
    }

    public function rules(): array
    {
        $tenantId = $this->tenantId();

        return [
            'tenant_id' => ['nullable', 'integer'],
            'company_id' => [
                'nullable',
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
            'warehouse_id' => [
                'nullable',
                'integer',
                Rule::exists('restaurant_warehouses', 'id')
                    ->when($tenantId, fn ($rule) => $rule->where('tenant_id', $tenantId)),
            ],
            'doc_no' => ['nullable', 'string', 'max:64'],
            'doc_type' => ['nullable', 'string', 'max:32'],
            'status' => ['nullable', 'string', 'max:32'],
            'doc_date' => ['nullable', 'date'],
            'reference_type' => ['nullable', 'string', 'max:255'],
            'reference_id' => ['nullable', 'integer'],
            'notes' => ['nullable', 'string'],
            'metadata' => ['nullable', 'array'],
            'lines' => ['nullable', 'array'],
            'lines.*.item_id' => [
                'required_with:lines',
                'integer',
                Rule::exists('restaurant_items', 'id')
                    ->when($tenantId, fn ($rule) => $rule->where('tenant_id', $tenantId)),
            ],
            'lines.*.uom_id' => [
                'nullable',
                'integer',
                Rule::exists('restaurant_uoms', 'id')
                    ->when($tenantId, fn ($rule) => $rule->where('tenant_id', $tenantId)),
            ],
            'lines.*.quantity' => ['required_with:lines', 'numeric', 'min:0.0001'],
            'lines.*.unit_cost' => ['nullable', 'numeric', 'min:0'],
            'lines.*.batch_no' => ['nullable', 'string', 'max:64'],
            'lines.*.expires_at' => ['nullable', 'date'],
            'lines.*.metadata' => ['nullable', 'array'],
        ];
    }
}
