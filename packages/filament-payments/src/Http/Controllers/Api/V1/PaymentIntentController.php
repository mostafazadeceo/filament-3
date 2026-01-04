<?php

namespace Haida\FilamentPayments\Http\Controllers\Api\V1;

use Filamat\IamSuite\Support\TenantContext;
use Haida\FilamentPayments\Http\Requests\StorePaymentIntentRequest;
use Haida\FilamentPayments\Http\Resources\PaymentIntentResource;
use Haida\FilamentPayments\Models\PaymentIntent;
use Haida\FilamentPayments\Services\PaymentIntentService;

class PaymentIntentController extends ApiController
{
    public function __construct()
    {
        $this->authorizeResource(PaymentIntent::class, 'intent');
    }

    public function store(StorePaymentIntentRequest $request, PaymentIntentService $service): PaymentIntentResource
    {
        $data = $request->validated();
        $payload = $data['provider_payload'] ?? [];
        unset($data['provider_payload']);

        $data['tenant_id'] = TenantContext::getTenantId();
        $data['created_by_user_id'] = auth()->id();

        $intent = $service->createIntent($data, $payload);

        return new PaymentIntentResource($intent);
    }

    public function show(PaymentIntent $intent): PaymentIntentResource
    {
        return new PaymentIntentResource($intent);
    }
}
