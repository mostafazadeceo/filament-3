<?php

namespace Haida\FilamentWorkhub\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EntityReferenceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'target_type' => ['required', 'string'],
            'target_id' => ['required', 'integer'],
            'relation_type' => ['nullable', 'string', 'max:255'],
        ];
    }
}
