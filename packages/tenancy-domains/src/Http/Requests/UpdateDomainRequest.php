<?php

namespace Haida\TenancyDomains\Http\Requests;

use Haida\TenancyDomains\Models\SiteDomain;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateDomainRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'site_id' => ['nullable', 'integer'],
            'verification_method' => ['nullable', 'in:txt,cname'],
            'service' => ['nullable', Rule::in(SiteDomain::services())],
            'is_primary' => ['nullable', 'boolean'],
        ];
    }
}
