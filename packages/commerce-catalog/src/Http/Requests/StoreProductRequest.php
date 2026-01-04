<?php

namespace Haida\CommerceCatalog\Http\Requests;

use Filamat\IamSuite\Support\TenantContext;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreProductRequest extends FormRequest
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

        return [
            'tenant_id' => ['required', 'exists:tenants,id'],
            'site_id' => [
                'required',
                Rule::exists('sites', 'id')->where('tenant_id', $tenantId),
            ],
            'name' => ['required', 'string', 'max:255'],
            'slug' => [
                'required',
                'string',
                'max:255',
                Rule::unique($productsTable, 'slug')->where('site_id', $this->input('site_id')),
            ],
            'type' => ['required', Rule::in(['physical', 'digital_code', 'downloadable', 'service', 'subscription', 'bundle', 'gift_card'])],
            'status' => ['nullable', Rule::in(['draft', 'published'])],
            'sku' => ['nullable', 'string', 'max:120'],
            'summary' => ['nullable', 'string'],
            'description' => ['nullable', 'string'],
            'currency' => ['nullable', 'string', 'max:8'],
            'price' => ['required', 'numeric', 'min:0'],
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
                Rule::exists($collectionsTable, 'id')->where('site_id', $this->input('site_id')),
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
