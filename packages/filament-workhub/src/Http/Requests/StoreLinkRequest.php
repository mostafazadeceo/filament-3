<?php

namespace Haida\FilamentWorkhub\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreLinkRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'target_type' => ['required', 'string', 'max:255'],
            'target_id' => ['required', 'integer'],
            'relation_type' => ['nullable', 'string', 'max:255'],
        ];
    }
}
