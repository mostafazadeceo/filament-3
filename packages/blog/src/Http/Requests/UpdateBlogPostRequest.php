<?php

namespace Haida\Blog\Http\Requests;

use Filamat\IamSuite\Support\TenantContext;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateBlogPostRequest extends FormRequest
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
        $post = $this->route('post');
        $postId = $post?->getKey();
        $siteId = $this->input('site_id', $post?->site_id);

        return [
            'site_id' => [
                'sometimes',
                Rule::exists('sites', 'id')->where('tenant_id', $tenantId),
            ],
            'category_id' => [
                'nullable',
                Rule::exists($categoriesTable, 'id')->where('site_id', $siteId),
            ],
            'title' => ['sometimes', 'string', 'max:255'],
            'slug' => [
                'sometimes',
                'string',
                'max:255',
                Rule::unique($postsTable, 'slug')->ignore($postId)->where('site_id', $siteId),
            ],
            'excerpt' => ['nullable', 'string'],
            'seo' => ['nullable', 'array'],
            'draft_content' => ['sometimes', 'string'],
            'status' => ['nullable', Rule::in(['draft', 'published'])],
            'tags' => ['nullable', 'array'],
            'tags.*' => [
                'integer',
                Rule::exists($tagsTable, 'id')->where('site_id', $siteId),
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
