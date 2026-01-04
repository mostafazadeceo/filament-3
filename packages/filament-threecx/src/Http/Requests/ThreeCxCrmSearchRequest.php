<?php

namespace Haida\FilamentThreeCx\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ThreeCxCrmSearchRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function prepareForValidation(): void
    {
        if ($this->has('q') && ! $this->has('query')) {
            $this->merge(['query' => $this->get('q')]);
        }
    }

    public function rules(): array
    {
        return [
            'query' => ['required', 'string', 'max:255'],
        ];
    }
}
