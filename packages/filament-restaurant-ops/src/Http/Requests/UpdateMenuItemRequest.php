<?php

namespace Haida\FilamentRestaurantOps\Http\Requests;

use Haida\FilamentRestaurantOps\Models\RestaurantMenuItem;
use Illuminate\Validation\Rule;

class UpdateMenuItemRequest extends BaseRestaurantOpsRequest
{
    public function authorize(): bool
    {
        $menuItem = $this->route('menu_item');

        return $menuItem
            ? auth()->user()?->can('update', $menuItem) ?? false
            : auth()->user()?->can('update', RestaurantMenuItem::class) ?? false;
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
            'recipe_id' => [
                'nullable',
                'integer',
                Rule::exists('restaurant_recipes', 'id')
                    ->when($tenantId, fn ($rule) => $rule->where('tenant_id', $tenantId)),
            ],
            'name' => ['nullable', 'string', 'max:255'],
            'code' => ['nullable', 'string', 'max:64'],
            'category' => ['nullable', 'string', 'max:255'],
            'price' => ['nullable', 'numeric', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
            'metadata' => ['nullable', 'array'],
        ];
    }
}
