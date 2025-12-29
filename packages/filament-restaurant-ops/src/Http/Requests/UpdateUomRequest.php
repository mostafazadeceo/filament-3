<?php

namespace Haida\FilamentRestaurantOps\Http\Requests;

use Haida\FilamentRestaurantOps\Models\RestaurantUom;
use Illuminate\Validation\Rule;

class UpdateUomRequest extends BaseRestaurantOpsRequest
{
    public function authorize(): bool
    {
        $uom = $this->route('uom');

        return $uom
            ? auth()->user()?->can('update', $uom) ?? false
            : auth()->user()?->can('update', RestaurantUom::class) ?? false;
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
            'name' => ['nullable', 'string', 'max:255'],
            'symbol' => ['nullable', 'string', 'max:32'],
            'is_base' => ['nullable', 'boolean'],
        ];
    }
}
