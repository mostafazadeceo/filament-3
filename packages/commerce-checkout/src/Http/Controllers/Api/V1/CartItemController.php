<?php

namespace Haida\CommerceCheckout\Http\Controllers\Api\V1;

use Filamat\IamSuite\Support\TenantContext;
use Haida\CommerceCatalog\Models\CatalogProduct;
use Haida\CommerceCatalog\Models\CatalogVariant;
use Haida\CommerceCheckout\Http\Requests\StoreCartItemRequest;
use Haida\CommerceCheckout\Http\Requests\UpdateCartItemRequest;
use Haida\CommerceCheckout\Http\Resources\CartResource;
use Haida\CommerceCheckout\Models\Cart;
use Haida\CommerceCheckout\Models\CartItem;
use Haida\CommerceCheckout\Services\CartService;
use Haida\FeatureGates\Services\FeatureGateService;
use Illuminate\Validation\ValidationException;

class CartItemController extends ApiController
{
    public function __construct(protected CartService $service) {}

    public function store(StoreCartItemRequest $request, Cart $cart): CartResource
    {
        $this->ensureFeature('commerce.cart.manage');
        $this->authorize('update', $cart);

        $data = $request->validated();
        $product = CatalogProduct::query()
            ->where('site_id', $cart->site_id)
            ->findOrFail((int) $data['product_id']);

        $variant = null;
        if (! empty($data['variant_id'])) {
            $variant = CatalogVariant::query()
                ->where('product_id', $product->getKey())
                ->findOrFail((int) $data['variant_id']);
        }

        $this->service->addItem(
            $cart,
            $product,
            $variant,
            (float) $data['quantity'],
            $data['meta'] ?? []
        );

        return new CartResource($cart->refresh()->loadMissing('items'));
    }

    public function update(UpdateCartItemRequest $request, CartItem $item): CartResource
    {
        $this->ensureFeature('commerce.cart.manage');
        $this->authorize('update', $item);

        $data = $request->validated();

        $this->service->updateItem($item, (float) $data['quantity']);

        return new CartResource($item->cart->refresh()->loadMissing('items'));
    }

    public function destroy(CartItem $item): CartResource
    {
        $this->ensureFeature('commerce.cart.manage');
        $this->authorize('delete', $item);

        $cart = $item->cart;
        $this->service->removeItem($item);

        return new CartResource($cart->refresh()->loadMissing('items'));
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
