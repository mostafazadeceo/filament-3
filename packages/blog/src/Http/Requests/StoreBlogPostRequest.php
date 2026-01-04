<?php

namespace Haida\Blog\Http\Requests;

use Filamat\IamSuite\Support\TenantContext;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreBlogPostRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $tenantId = TenantContext::getTenantId();
        $postsTable = config('blog.tables.posts', 'blog_posts');
        $categoriesTable = config('blog.tables.categories', 'blog_categories');
        $tagsTable = config('blog.tables.tags', 'blog_tags');

        return [
            'tenant_id' => ['required', 'exists:tenants,id'],
            'site_id' => [
                'required',
                Rule::exists('sites', 'id')->where('tenant_id', $tenantId),
            ],
            'category_id' => [
                'nullable',
                Rule::exists($categoriesTable, 'id')->where('site_id', $this->input('site_id')),
            ],
            'title' => ['required', 'string', 'max:255'],
            'slug' => [
                'required',
                'string',
                'max:255',
                Rule::unique($postsTable, 'slug')->where('site_id', $this->input('site_id')),
            ],
            'excerpt' => ['nullable', 'string'],
            'seo' => ['nullable', 'array'],
            'draft_content' => ['required', 'string'],
            'status' => ['nullable', Rule::in(['draft', 'published'])],
            'tags' => ['nullable', 'array'],
            'tags.*' => [
                'integer',
                Rule::exists($tagsTable, 'id')->where('site_id', $this->input('site_id')),
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
