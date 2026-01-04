<?php

namespace Haida\TenancyDomains\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

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
            'is_primary' => ['nullable', 'boolean'],
        ];
    }
}
