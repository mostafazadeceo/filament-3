<?php

namespace Haida\FilamentThreeCx\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ThreeCxCrmContactRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['nullable', 'string', 'max:255'],
            'phones' => ['nullable', 'array'],
            'phones.*' => ['nullable', 'string', 'max:64'],
            'emails' => ['nullable', 'array'],
            'emails.*' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:64'],
            'email' => ['nullable', 'email', 'max:255'],
            'external_id' => ['nullable', 'string', 'max:255'],
            'crm_url' => ['nullable', 'string', 'max:2048'],
        ];
    }
}
