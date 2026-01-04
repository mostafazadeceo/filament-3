<?php

namespace Haida\ContentCms\Http\Requests;

use Filamat\IamSuite\Support\TenantContext;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $tenantId = TenantContext::getTenantId();
        $pagesTable = config('content-cms.tables.pages', 'content_cms_pages');

        return [
            'tenant_id' => ['required', 'exists:tenants,id'],
            'site_id' => [
                'required',
                Rule::exists('sites', 'id')->where('tenant_id', $tenantId),
            ],
            'slug' => [
                'required',
                'string',
                'max:255',
                Rule::unique($pagesTable, 'slug')->where('site_id', $this->input('site_id')),
            ],
            'title' => ['required', 'string', 'max:255'],
            'seo' => ['nullable', 'array'],
            'draft_content' => ['required', 'array'],
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
