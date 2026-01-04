<?php

namespace Haida\CommerceCatalog\Http\Requests;

use Filamat\IamSuite\Support\TenantContext;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $tenantId = TenantContext::getTenantId();
        $productsTable = config('commerce-catalog.tables.products', 'commerce_catalog_products');
        $collectionsTable = config('commerce-catalog.tables.collections', 'commerce_catalog_collections');
        $product = $this->route('product');
        $productId = $product?->getKey();
        $siteId = $this->input('site_id', $product?->site_id);

        return [
            'site_id' => [
                'sometimes',
                Rule::exists('sites', 'id')->where('tenant_id', $tenantId),
            ],
            'name' => ['sometimes', 'string', 'max:255'],
            'slug' => [
                'sometimes',
                'string',
                'max:255',
                Rule::unique($productsTable, 'slug')->ignore($productId)->where('site_id', $siteId),
            ],
            'type' => ['sometimes', Rule::in(['physical', 'digital_code', 'downloadable', 'service', 'subscription', 'bundle', 'gift_card'])],
            'status' => ['nullable', Rule::in(['draft', 'published'])],
            'sku' => ['nullable', 'string', 'max:120'],
            'summary' => ['nullable', 'string'],
            'description' => ['nullable', 'string'],
            'currency' => ['nullable', 'string', 'max:8'],
            'price' => ['sometimes', 'numeric', 'min:0'],
            'compare_at_price' => ['nullable', 'numeric', 'min:0'],
            'track_inventory' => ['nullable', 'boolean'],
            'accounting_product_id' => [
                'nullable',
                Rule::exists('accounting_ir_products_services', 'id')->where('tenant_id', $tenantId),
            ],
            'inventory_item_id' => [
                'nullable',
                Rule::exists('accounting_ir_inventory_items', 'id')->where('tenant_id', $tenantId),
            ],
            'metadata' => ['nullable', 'array'],
            'collections' => ['nullable', 'array'],
            'collections.*' => [
                'integer',
                Rule::exists($collectionsTable, 'id')->where('site_id', $siteId),
            ],
        ];
    }

    protected function prepareForValidation(): void
    {
        if (! $this->has('tenant_id')) {
            $this->merge([
                'tenant_id' => TenantContext::getTenantId(),
            ]);
        }
    }
}
