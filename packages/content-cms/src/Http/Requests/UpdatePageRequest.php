<?php

namespace Haida\ContentCms\Http\Requests;

use Filamat\IamSuite\Support\TenantContext;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $tenantId = TenantContext::getTenantId();
        $pagesTable = config('content-cms.tables.pages', 'content_cms_pages');
        $pageId = $this->route('page')?->getKey();

        return [
            'site_id' => [
                'sometimes',
                Rule::exists('sites', 'id')->where('tenant_id', $tenantId),
            ],
            'slug' => [
                'sometimes',
                'string',
                'max:255',
                Rule::unique($pagesTable, 'slug')
                    ->ignore($pageId)
                    ->where('site_id', $this->input('site_id', $this->route('page')?->site_id)),
            ],
            'title' => ['sometimes', 'string', 'max:255'],
            'seo' => ['nullable', 'array'],
            'draft_content' => ['sometimes', 'array'],
            'status' => ['nullable', Rule::in(['draft', 'published'])],
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
