<?php

namespace Haida\FilamentRestaurantOps\Http\Requests;

use Haida\FilamentRestaurantOps\Models\RestaurantGoodsReceipt;
use Illuminate\Validation\Rule;

class UpdateGoodsReceiptRequest extends BaseRestaurantOpsRequest
{
    public function authorize(): bool
    {
        $receipt = $this->route('goods_receipt');

        return $receipt
            ? auth()->user()?->can('update', $receipt) ?? false
            : auth()->user()?->can('update', RestaurantGoodsReceipt::class) ?? false;
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
            'supplier_id' => [
                'nullable',
                'integer',
                Rule::exists('restaurant_suppliers', 'id')
                    ->when($tenantId, fn ($rule) => $rule->where('tenant_id', $tenantId)),
            ],
            'purchase_order_id' => [
                'nullable',
                'integer',
                Rule::exists('restaurant_purchase_orders', 'id')
                    ->when($tenantId, fn ($rule) => $rule->where('tenant_id', $tenantId)),
            ],
            'receipt_no' => ['nullable', 'string', 'max:64'],
            'receipt_date' => ['nullable', 'date'],
            'status' => ['nullable', 'string', 'max:32'],
            'subtotal' => ['nullable', 'numeric', 'min:0'],
            'tax_total' => ['nullable', 'numeric', 'min:0'],
            'total' => ['nullable', 'numeric', 'min:0'],
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
            'lines.*.tax_rate' => ['nullable', 'numeric', 'min:0'],
            'lines.*.tax_amount' => ['nullable', 'numeric', 'min:0'],
            'lines.*.batch_no' => ['nullable', 'string', 'max:64'],
            'lines.*.expires_at' => ['nullable', 'date'],
            'lines.*.line_total' => ['nullable', 'numeric', 'min:0'],
        ];
    }
}
