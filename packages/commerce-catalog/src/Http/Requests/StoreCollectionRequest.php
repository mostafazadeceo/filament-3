<?php

namespace Haida\CommerceCatalog\Http\Requests;

use Filamat\IamSuite\Support\TenantContext;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCollectionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $tenantId = TenantContext::getTenantId();
        $collectionsTable = config('commerce-catalog.tables.collections', 'commerce_catalog_collections');
        $productsTable = config('commerce-catalog.tables.products', 'commerce_catalog_products');

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
                Rule::unique($collectionsTable, 'slug')->where('site_id', $this->input('site_id')),
            ],
            'status' => ['nullable', Rule::in(['draft', 'published'])],
            'description' => ['nullable', 'string'],
            'products' => ['nullable', 'array'],
            'products.*' => [
                'integer',
                Rule::exists($productsTable, 'id')->where('site_id', $this->input('site_id')),
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
