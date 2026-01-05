<?php

namespace Haida\CommerceCheckout\Http\Controllers\Api\V1;

use Filamat\IamSuite\Support\TenantContext;
use Haida\CommerceCheckout\Http\Requests\CheckoutRequest;
use Haida\CommerceCheckout\Models\Cart;
use Haida\CommerceCheckout\Services\CheckoutService;
use Haida\CommerceOrders\Http\Resources\OrderResource;
use Haida\FeatureGates\Services\FeatureGateService;
use Illuminate\Validation\ValidationException;

class CheckoutController extends ApiController
{
    public function __construct(protected CheckoutService $service) {}

    public function store(CheckoutRequest $request): OrderResource
    {
        $data = $request->validated();

        if (class_exists(FeatureGateService::class)) {
            $tenant = TenantContext::getTenant();
            if ($tenant) {
                $decision = app(FeatureGateService::class)->evaluate($tenant, 'commerce.checkout.create', null, $request->user());
                if (! $decision->allowed) {
                    throw ValidationException::withMessages(['feature' => 'این قابلیت برای پلن شما فعال نیست.']);
                }
            }
        }

        $cart = Cart::query()->findOrFail((int) $data['cart_id']);
        $this->authorize('update', $cart);

        $order = $this->service->checkout($cart, $request->user(), $data);

        return new OrderResource($order->loadMissing(['items', 'payments']));
    }
}
