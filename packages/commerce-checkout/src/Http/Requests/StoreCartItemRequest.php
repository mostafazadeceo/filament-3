<?php

namespace Haida\CommerceCheckout\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCartItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'product_id' => ['required', 'integer'],
            'variant_id' => ['nullable', 'integer'],
            'quantity' => ['required', 'numeric', 'min:0.0001'],
            'meta' => ['nullable', 'array'],
        ];
    }
}
