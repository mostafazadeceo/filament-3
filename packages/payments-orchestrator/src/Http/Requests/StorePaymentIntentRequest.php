<?php

namespace Haida\PaymentsOrchestrator\Http\Requests;

use Filamat\IamSuite\Support\TenantContext;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePaymentIntentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $tenantId = TenantContext::getTenantId();
        $providers = array_keys((array) config('payments-orchestrator.adapters', []));

        return [
            'order_id' => [
                'required',
                'integer',
                Rule::exists(config('commerce-orders.tables.orders', 'commerce_orders'), 'id')
                    ->where('tenant_id', $tenantId),
            ],
            'provider_key' => ['required', 'string', Rule::in($providers)],
            'idempotency_key' => ['required', 'string', 'max:255'],
            'return_url' => ['nullable', 'url', 'max:2048'],
            'meta' => ['nullable', 'array'],
        ];
    }
}
