<?php

declare(strict_types=1);

namespace Haida\FilamentCryptoGateway\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePayoutDestinationRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'label' => ['nullable', 'string', 'max:120'],
            'address' => ['nullable', 'string', 'max:255'],
            'currency' => ['nullable', 'string', 'max:16'],
            'network' => ['nullable', 'string', 'max:32'],
            'status' => ['nullable', 'string', 'in:active,inactive'],
            'meta' => ['nullable', 'array'],
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
