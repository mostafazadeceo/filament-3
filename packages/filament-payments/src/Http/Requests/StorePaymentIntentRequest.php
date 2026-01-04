<?php

namespace Haida\FilamentPayments\Http\Requests;

use Haida\FilamentPayments\Models\PaymentIntent;
use Illuminate\Validation\Rule;

class StorePaymentIntentRequest extends BasePaymentRequest
{
    public function authorize(): bool
    {
        return auth()->user()?->can('create', PaymentIntent::class) ?? false;
    }

    public function rules(): array
    {
        $providers = array_keys((array) config('filament-payments.providers', []));

        return [
            'provider' => ['nullable', 'string', Rule::in($providers)],
            'reference_type' => ['nullable', 'string', 'max:255'],
            'reference_id' => ['nullable', 'integer'],
            'currency' => ['nullable', 'string', 'max:8'],
            'amount' => ['required', 'numeric', 'min:0'],
            'idempotency_key' => ['nullable', 'string', 'max:255'],
            'expires_at' => ['nullable', 'date'],
            'metadata' => ['nullable', 'array'],
            'provider_payload' => ['nullable', 'array'],
        ];
    }
}
