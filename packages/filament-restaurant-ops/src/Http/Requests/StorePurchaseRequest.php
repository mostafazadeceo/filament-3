<?php

namespace Haida\FilamentRestaurantOps\Http\Requests;

use Haida\FilamentRestaurantOps\Models\RestaurantPurchaseRequest;
use Illuminate\Validation\Rule;

class StorePurchaseRequest extends BaseRestaurantOpsRequest
{
    public function authorize(): bool
    {
        return auth()->user()?->can('create', RestaurantPurchaseRequest::class) ?? false;
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
            'requested_by' => ['nullable', 'integer'],
            'status' => ['nullable', 'string', 'max:32'],
            'needed_at' => ['nullable', 'date'],
            'notes' => ['nullable', 'string'],
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
            'lines.*.notes' => ['nullable', 'string'],
        ];
    }
}
