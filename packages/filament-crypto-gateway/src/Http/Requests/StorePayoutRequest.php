<?php

declare(strict_types=1);

namespace Haida\FilamentCryptoGateway\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePayoutRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'provider' => ['required', 'string', 'max:50'],
            'order_id' => ['required', 'string', 'max:120'],
            'to_address' => ['required', 'string', 'max:255'],
            'amount' => ['required', 'numeric', 'min:0.00000001'],
            'currency' => ['required', 'string', 'max:16'],
            'network' => ['nullable', 'string', 'max:64'],
            'meta' => ['nullable', 'array'],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
