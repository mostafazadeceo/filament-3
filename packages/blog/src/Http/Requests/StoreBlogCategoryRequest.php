<?php

namespace Haida\Blog\Http\Requests;

use Filamat\IamSuite\Support\TenantContext;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreBlogCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $tenantId = TenantContext::getTenantId();
        $categoriesTable = config('blog.tables.categories', 'blog_categories');

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
                Rule::unique($categoriesTable, 'slug')->where('site_id', $this->input('site_id')),
            ],
            'description' => ['nullable', 'string'],
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
