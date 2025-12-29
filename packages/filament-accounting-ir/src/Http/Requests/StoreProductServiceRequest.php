<?php

namespace Vendor\FilamentAccountingIr\Http\Requests;

use Illuminate\Validation\Rule;
use Vendor\FilamentAccountingIr\Models\ProductService;

class StoreProductServiceRequest extends BaseAccountingRequest
{
    public function authorize(): bool
    {
        return auth()->user()?->can('create', ProductService::class) ?? false;
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
            'code' => ['nullable', 'string', 'max:64'],
            'name' => ['required', 'string', 'max:255'],
            'item_type' => ['required', 'string', 'max:32'],
            'uom_id' => ['nullable', 'integer', Rule::exists('accounting_ir_uoms', 'id')],
            'tax_category_id' => ['nullable', 'integer', Rule::exists('accounting_ir_tax_categories', 'id')],
            'base_price' => ['nullable', 'numeric', 'min:0'],
            'is_active' => ['boolean'],
        ];
    }
}
