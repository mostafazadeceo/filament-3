<?php

namespace Haida\Blog\Http\Requests;

use Filamat\IamSuite\Support\TenantContext;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateBlogCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $tenantId = TenantContext::getTenantId();
        $categoriesTable = config('blog.tables.categories', 'blog_categories');
        $category = $this->route('category');
        $categoryId = $category?->getKey();
        $siteId = $this->input('site_id', $category?->site_id);

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
                Rule::unique($categoriesTable, 'slug')->ignore($categoryId)->where('site_id', $siteId),
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
