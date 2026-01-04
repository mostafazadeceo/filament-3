<?php

declare(strict_types=1);

namespace Haida\MailtrapCore\Http\Requests;

use Filamat\IamSuite\Support\TenantContext;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateOfferRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'tenant_id' => ['required', 'exists:tenants,id'],
            'name' => ['sometimes', 'string', 'max:150'],
            'slug' => ['nullable', 'string', 'max:200'],
            'status' => ['nullable', Rule::in(['active', 'inactive'])],
            'description' => ['nullable', 'string'],
            'duration_days' => ['sometimes', 'integer', 'min:1'],
            'feature_keys' => ['sometimes', 'array'],
            'feature_keys.*' => ['string', 'max:150'],
            'limits' => ['nullable', 'array'],
            'price' => ['sometimes', 'numeric', 'min:0'],
            'currency' => ['sometimes', 'string', 'max:10'],
            'metadata' => ['nullable', 'array'],
            'publish_to_catalog' => ['nullable', 'boolean'],
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
