<?php

namespace Haida\FilamentRestaurantOps\Http\Requests;

use Haida\FilamentRestaurantOps\Models\RestaurantMenuSale;
use Illuminate\Validation\Rule;

class UpdateMenuSaleRequest extends BaseRestaurantOpsRequest
{
    public function authorize(): bool
    {
        $sale = $this->route('menu_sale');

        return $sale
            ? auth()->user()?->can('update', $sale) ?? false
            : auth()->user()?->can('update', RestaurantMenuSale::class) ?? false;
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
            'sale_date' => ['nullable', 'date'],
            'source' => ['nullable', 'string', 'max:32'],
            'external_ref' => ['nullable', 'string', 'max:255'],
            'total_amount' => ['nullable', 'numeric', 'min:0'],
            'status' => ['nullable', 'string', 'max:32'],
            'lines' => ['nullable', 'array'],
            'lines.*.menu_item_id' => [
                'required_with:lines',
                'integer',
                Rule::exists('restaurant_menu_items', 'id')
                    ->when($tenantId, fn ($rule) => $rule->where('tenant_id', $tenantId)),
            ],
            'lines.*.quantity' => ['required_with:lines', 'numeric', 'min:0.0001'],
            'lines.*.unit_price' => ['nullable', 'numeric', 'min:0'],
            'lines.*.line_total' => ['nullable', 'numeric', 'min:0'],
        ];
    }
}
