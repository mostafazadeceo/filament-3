<?php

namespace Haida\CommerceCheckout\Services;

use Haida\CommerceCatalog\Models\CatalogProduct;
use Haida\CommerceCatalog\Models\CatalogVariant;
use Haida\CommerceCheckout\Models\Cart;
use Haida\CommerceCheckout\Models\CartItem;
use Haida\SiteBuilderCore\Models\Site;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class CartService
{
    public function getOrCreateCart(int $tenantId, Site $site, ?int $userId = null): Cart
    {
        $query = Cart::query()
            ->where('tenant_id', $tenantId)
            ->where('site_id', $site->getKey())
            ->where('status', 'active');

        if ($userId) {
            $query->where('user_id', $userId);
        } else {
            $query->whereNull('user_id');
        }

        $cart = $query->first();
        if ($cart) {
            return $cart;
        }

        $expiresAt = $this->resolveExpiry();

        return Cart::query()->create([
            'tenant_id' => $tenantId,
            'site_id' => $site->getKey(),
            'user_id' => $userId,
            'status' => 'active',
            'currency' => $site->currency ?? config('commerce-catalog.defaults.currency', 'IRR'),
            'subtotal' => 0,
            'discount_total' => 0,
            'tax_total' => 0,
            'shipping_total' => 0,
            'total' => 0,
            'expires_at' => $expiresAt,
            'meta' => [
                'token' => (string) Str::uuid(),
            ],
        ]);
    }

    public function addItem(Cart $cart, CatalogProduct $product, ?CatalogVariant $variant, float $quantity, array $meta = []): CartItem
    {
        $this->assertCartActive($cart);

        if ($product->status !== 'published') {
            throw ValidationException::withMessages(['product_id' => 'محصول قابل خرید نیست.']);
        }

        if ($quantity <= 0) {
            throw ValidationException::withMessages(['quantity' => 'تعداد باید بیشتر از صفر باشد.']);
        }

        $currency = $variant?->currency ?? $product->currency;
        if ($currency !== $cart->currency) {
            throw ValidationException::withMessages(['currency' => 'ارز سبد خرید با آیتم همخوانی ندارد.']);
        }

        $unitPrice = (float) ($variant?->price ?? $product->price);

        $item = CartItem::query()
            ->where('cart_id', $cart->getKey())
            ->where('product_id', $product->getKey())
            ->where('variant_id', $variant?->getKey())
            ->first();

        if ($item) {
            $item->quantity = (float) $item->quantity + $quantity;
            $item->unit_price = $unitPrice;
            $item->currency = $currency;
            $item->line_total = $this->calculateLineTotal($item->quantity, $unitPrice);
            $item->meta = array_merge($item->meta ?? [], $meta);
            $item->save();
        } else {
            $item = CartItem::query()->create([
                'tenant_id' => $cart->tenant_id,
                'cart_id' => $cart->getKey(),
                'product_id' => $product->getKey(),
                'variant_id' => $variant?->getKey(),
                'name' => $variant?->name ?? $product->name,
                'sku' => $variant?->sku ?? $product->sku,
                'quantity' => $quantity,
                'currency' => $currency,
                'unit_price' => $unitPrice,
                'line_total' => $this->calculateLineTotal($quantity, $unitPrice),
                'meta' => $meta,
            ]);
        }

        $this->recalculate($cart);

        return $item;
    }

    public function updateItem(CartItem $item, float $quantity): CartItem
    {
        $this->assertCartActive($item->cart);

        if ($quantity <= 0) {
            $item->delete();
            $this->recalculate($item->cart);

            return $item;
        }

        $item->quantity = $quantity;
        $item->line_total = $this->calculateLineTotal($quantity, (float) $item->unit_price);
        $item->save();

        $this->recalculate($item->cart);

        return $item;
    }

    public function removeItem(CartItem $item): void
    {
        $this->assertCartActive($item->cart);

        $cart = $item->cart;
        $item->delete();
        $this->recalculate($cart);
    }

    public function recalculate(Cart $cart): void
    {
        $subtotal = (float) $cart->items()->sum('line_total');
        $total = $subtotal - (float) $cart->discount_total + (float) $cart->tax_total + (float) $cart->shipping_total;

        $cart->update([
            'subtotal' => $subtotal,
            'total' => $total,
        ]);
    }

    private function assertCartActive(Cart $cart): void
    {
        if ($cart->status !== 'active') {
            throw ValidationException::withMessages(['cart' => 'سبد خرید فعال نیست.']);
        }
    }

    private function calculateLineTotal(float $quantity, float $unitPrice): float
    {
        return round($quantity * $unitPrice, 4);
    }

    private function resolveExpiry(): ?Carbon
    {
        $days = (int) config('commerce-checkout.cart.expires_after_days', 7);
        if ($days <= 0) {
            return null;
        }

        return now()->addDays($days);
    }
}
