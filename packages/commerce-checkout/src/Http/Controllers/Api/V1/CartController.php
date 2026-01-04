<?php

namespace Haida\CommerceCheckout\Http\Controllers\Api\V1;

use Filamat\IamSuite\Support\IamAuthorization;
use Filamat\IamSuite\Support\TenantContext;
use Haida\CommerceCheckout\Http\Requests\StoreCartRequest;
use Haida\CommerceCheckout\Http\Resources\CartResource;
use Haida\CommerceCheckout\Models\Cart;
use Haida\CommerceCheckout\Services\CartService;
use Haida\FeatureGates\Services\FeatureGateService;
use Haida\SiteBuilderCore\Models\Site;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Validation\ValidationException;

class CartController extends ApiController
{
    public function __construct(protected CartService $service)
    {
        $this->authorizeResource(Cart::class, 'cart');
    }

    public function index(): AnonymousResourceCollection
    {
        $this->ensureFeature('commerce.cart.view');

        $query = Cart::query()->with('items')->latest();

        if (! IamAuthorization::allows('commerce.cart.manage')) {
            $query->where('user_id', auth()->id());
        }

        return CartResource::collection($query->paginate());
    }

    public function store(StoreCartRequest $request): CartResource
    {
        $this->ensureFeature('commerce.cart.manage');

        $tenant = TenantContext::getTenant();
        if (! $tenant) {
            throw ValidationException::withMessages(['tenant' => 'تننت انتخاب نشده است.']);
        }

        $site = Site::query()->findOrFail((int) $request->validated('site_id'));

        $cart = $this->service->getOrCreateCart(
            $tenant->getKey(),
            $site,
            auth()->id()
        );

        return new CartResource($cart->loadMissing('items'));
    }

    public function show(Cart $cart): CartResource
    {
        $this->ensureFeature('commerce.cart.view');

        return new CartResource($cart->loadMissing('items'));
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
