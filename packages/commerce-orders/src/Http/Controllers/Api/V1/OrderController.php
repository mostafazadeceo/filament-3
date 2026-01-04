<?php

namespace Haida\CommerceOrders\Http\Controllers\Api\V1;

use Haida\CommerceOrders\Http\Requests\UpdateOrderRequest;
use Haida\CommerceOrders\Http\Resources\OrderResource;
use Haida\CommerceOrders\Models\Order;
use Haida\FeatureGates\Services\FeatureGateService;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Filamat\IamSuite\Support\TenantContext;
use Illuminate\Validation\ValidationException;

class OrderController extends ApiController
{
    public function __construct()
    {
        $this->authorizeResource(Order::class, 'order');
    }

    public function index(): AnonymousResourceCollection
    {
        $this->ensureFeature('commerce.order.view');

        $orders = Order::query()
            ->with(['items', 'payments'])
            ->latest()
            ->paginate();

        return OrderResource::collection($orders);
    }

    public function show(Order $order): OrderResource
    {
        $this->ensureFeature('commerce.order.view');

        return new OrderResource($order->loadMissing(['items', 'payments']));
    }

    public function update(UpdateOrderRequest $request, Order $order): OrderResource
    {
        $this->ensureFeature('commerce.order.manage');

        $order->update($request->validated());

        return new OrderResource($order->refresh()->loadMissing(['items', 'payments']));
    }

    private function ensureFeature(string $feature): void
    {
        if (! class_exists(FeatureGateService::class)) {
            return;
        }

        $tenant = TenantContext::getTenant();
        if (! $tenant) {
            return;
        }

        $decision = app(FeatureGateService::class)->evaluate($tenant, $feature, null, auth()->user());
        if (! $decision->allowed) {
            throw ValidationException::withMessages(['feature' => 'این قابلیت برای پلن شما فعال نیست.']);
        }
    }
}
