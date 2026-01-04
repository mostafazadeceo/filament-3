<?php

declare(strict_types=1);

namespace Haida\FilamentCryptoGateway\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreInvoiceRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'provider' => ['required', 'string', 'max:50'],
            'order_id' => ['required', 'string', 'max:120'],
            'amount' => ['required', 'numeric', 'min:0.00000001'],
            'currency' => ['required', 'string', 'max:16'],
            'to_currency' => ['nullable', 'string', 'max:16'],
            'network' => ['nullable', 'string', 'max:64'],
            'is_payment_multiple' => ['nullable', 'boolean'],
            'lifetime' => ['nullable', 'integer', 'min:60'],
            'callback_url' => ['nullable', 'url'],
            'tolerance_percent' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'accuracy_payment_percent' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'subtract_percent' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'subtract' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'meta' => ['nullable', 'array'],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
