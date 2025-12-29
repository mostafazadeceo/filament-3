<?php

namespace Haida\FilamentRestaurantOps\Http\Requests;

use Haida\FilamentRestaurantOps\Models\RestaurantRecipe;
use Illuminate\Validation\Rule;

class StoreRecipeRequest extends BaseRestaurantOpsRequest
{
    public function authorize(): bool
    {
        return auth()->user()?->can('create', RestaurantRecipe::class) ?? false;
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
            'name' => ['required', 'string', 'max:255'],
            'code' => ['nullable', 'string', 'max:64'],
            'yield_quantity' => ['nullable', 'numeric', 'min:0'],
            'yield_uom_id' => [
                'nullable',
                'integer',
                Rule::exists('restaurant_uoms', 'id')
                    ->when($tenantId, fn ($rule) => $rule->where('tenant_id', $tenantId)),
            ],
            'waste_percent' => ['nullable', 'numeric', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
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
            'lines.*.waste_percent' => ['nullable', 'numeric', 'min:0'],
            'lines.*.is_optional' => ['nullable', 'boolean'],
        ];
    }
}
