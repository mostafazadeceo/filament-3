<?php

namespace Haida\TenancyDomains\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDomainRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'site_id' => ['nullable', 'integer'],
            'host' => DomainRules::hostRules(),
            'type' => ['nullable', 'in:custom,subdomain'],
            'verification_method' => ['nullable', 'in:txt,cname'],
            'is_primary' => ['nullable', 'boolean'],
        ];
    }
}
