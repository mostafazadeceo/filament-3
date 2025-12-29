<?php

namespace Vendor\FilamentAccountingIr\Http\Requests;

use Illuminate\Validation\Rule;
use Vendor\FilamentAccountingIr\Models\ProductService;

class UpdateProductServiceRequest extends BaseAccountingRequest
{
    public function authorize(): bool
    {
        $product = $this->route('product_service');

        return $product instanceof ProductService
            ? auth()->user()?->can('update', $product)
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
            'code' => ['nullable', 'string', 'max:64'],
            'name' => ['sometimes', 'string', 'max:255'],
            'item_type' => ['sometimes', 'string', 'max:32'],
            'uom_id' => ['nullable', 'integer', Rule::exists('accounting_ir_uoms', 'id')],
            'tax_category_id' => ['nullable', 'integer', Rule::exists('accounting_ir_tax_categories', 'id')],
            'base_price' => ['nullable', 'numeric', 'min:0'],
            'is_active' => ['boolean'],
        ];
    }
}
