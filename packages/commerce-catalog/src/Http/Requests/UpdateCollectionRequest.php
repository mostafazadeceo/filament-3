<?php

namespace Haida\CommerceCatalog\Http\Requests;

use Filamat\IamSuite\Support\TenantContext;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCollectionRequest extends FormRequest
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
        $collection = $this->route('collection');
        $collectionId = $collection?->getKey();
        $siteId = $this->input('site_id', $collection?->site_id);

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
                Rule::unique($collectionsTable, 'slug')->ignore($collectionId)->where('site_id', $siteId),
            ],
            'status' => ['nullable', Rule::in(['draft', 'published'])],
            'description' => ['nullable', 'string'],
            'products' => ['nullable', 'array'],
            'products.*' => [
                'integer',
                Rule::exists($productsTable, 'id')->where('site_id', $siteId),
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
