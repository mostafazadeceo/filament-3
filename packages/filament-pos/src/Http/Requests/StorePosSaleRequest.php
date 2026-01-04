<?php

namespace Haida\FilamentPos\Http\Requests;

use Haida\FilamentPos\Models\PosSale;
use Illuminate\Validation\Rule;

class StorePosSaleRequest extends BasePosRequest
{
    public function authorize(): bool
    {
        return auth()->user()?->can('create', PosSale::class) ?? false;
    }

    public function rules(): array
    {
        return [
            'store_id' => ['required', 'integer'],
            'register_id' => ['required', 'integer'],
            'session_id' => ['nullable', 'integer'],
            'device_id' => ['nullable', 'integer'],
            'receipt_no' => ['nullable', 'string', 'max:255'],
            'status' => ['nullable', 'string', 'max:32'],
            'payment_status' => ['nullable', 'string', 'max:32'],
            'currency' => ['nullable', 'string', 'max:8'],
            'idempotency_key' => ['nullable', 'string', 'max:255'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.name' => ['required', 'string', 'max:255'],
            'items.*.quantity' => ['nullable', 'numeric', 'min:0.0001'],
            'items.*.unit_price' => ['nullable', 'numeric', 'min:0'],
            'items.*.discount_amount' => ['nullable', 'numeric', 'min:0'],
            'items.*.tax_amount' => ['nullable', 'numeric', 'min:0'],
            'payments' => ['nullable', 'array'],
            'payments.*.provider' => ['nullable', 'string', 'max:64'],
            'payments.*.amount' => ['nullable', 'numeric', 'min:0'],
            'payments.*.currency' => ['nullable', 'string', 'max:8'],
            'payments.*.status' => ['nullable', 'string', 'max:32'],
            'metadata' => ['nullable', 'array'],
        ];
    }
}
