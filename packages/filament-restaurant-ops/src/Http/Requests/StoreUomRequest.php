<?php

namespace Haida\FilamentRestaurantOps\Http\Requests;

use Haida\FilamentRestaurantOps\Models\RestaurantUom;
use Illuminate\Validation\Rule;

class StoreUomRequest extends BaseRestaurantOpsRequest
{
    public function authorize(): bool
    {
        return auth()->user()?->can('create', RestaurantUom::class) ?? false;
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
            'symbol' => ['nullable', 'string', 'max:32'],
            'is_base' => ['nullable', 'boolean'],
        ];
    }
}
