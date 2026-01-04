<?php

namespace Haida\PaymentsOrchestrator\Http\Controllers\Api\V1;

use Haida\CommerceOrders\Models\Order;
use Haida\PaymentsOrchestrator\Http\Requests\StorePaymentIntentRequest;
use Haida\PaymentsOrchestrator\Http\Resources\PaymentIntentResource;
use Haida\PaymentsOrchestrator\Models\PaymentGatewayConnection;
use Haida\PaymentsOrchestrator\Models\PaymentIntent;
use Haida\PaymentsOrchestrator\Services\PaymentIntentService;
use Illuminate\Validation\ValidationException;

class PaymentIntentController extends ApiController
{
    public function __construct(protected PaymentIntentService $service)
    {
        $this->authorizeResource(PaymentIntent::class, 'intent');
    }

    public function store(StorePaymentIntentRequest $request): PaymentIntentResource
    {
        $data = $request->validated();

        $order = Order::query()->findOrFail((int) $data['order_id']);

        $connection = PaymentGatewayConnection::query()
            ->where('tenant_id', $order->tenant_id)
            ->where('provider_key', $data['provider_key'])
            ->where('is_active', true)
            ->first();

        if (! $connection) {
            throw ValidationException::withMessages(['provider_key' => 'درگاه فعال یافت نشد.']);
        }

        $intent = $this->service->createIntent($order, $connection, $data);

        return new PaymentIntentResource($intent);
    }

    public function show(PaymentIntent $intent): PaymentIntentResource
    {
        return new PaymentIntentResource($intent);
    }
}
