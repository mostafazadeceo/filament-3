<?php

namespace Haida\CommerceCheckout\Services;

use Filamat\IamSuite\Models\Tenant;
use Filamat\IamSuite\Models\Wallet;
use Filamat\IamSuite\Services\WalletService;
use Haida\CommerceCheckout\Models\Cart;
use Haida\CommerceCheckout\Services\OrderInventoryService;
use Haida\CommerceOrders\Events\OrderPaid;
use Haida\CommerceOrders\Events\OrderPlaced;
use Haida\CommerceOrders\Models\Order;
use Haida\CommerceOrders\Models\OrderPayment;
use Haida\CommerceOrders\Services\OrderNumberGenerator;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\DatabaseManager;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class CheckoutService
{
    public function __construct(
        protected DatabaseManager $db,
        protected OrderNumberGenerator $numberGenerator,
        protected WalletService $walletService,
        protected OrderInventoryService $inventoryService
    ) {
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    public function checkout(Cart $cart, ?Authenticatable $user, array $payload = []): Order
    {
        $idempotencyKey = $payload['idempotency_key'] ?? null;

        if ($idempotencyKey) {
            $existing = Order::query()
                ->where('tenant_id', $cart->tenant_id)
                ->where('idempotency_key', $idempotencyKey)
                ->first();

            if ($existing) {
                return $existing->loadMissing(['items', 'payments']);
            }
        }

        return $this->db->transaction(function () use ($cart, $user, $payload, $idempotencyKey): Order {
            $cart = Cart::query()->with('items')->lockForUpdate()->findOrFail($cart->getKey());

            if ($cart->status !== 'active') {
                throw ValidationException::withMessages(['cart' => 'سبد خرید فعال نیست.']);
            }

            if ($cart->items->isEmpty()) {
                throw ValidationException::withMessages(['cart' => 'سبد خرید خالی است.']);
            }

            $order = Order::query()->create([
                'tenant_id' => $cart->tenant_id,
                'site_id' => $cart->site_id,
                'user_id' => $user?->getAuthIdentifier(),
                'cart_id' => $cart->getKey(),
                'status' => 'pending',
                'payment_status' => 'pending',
                'currency' => $cart->currency,
                'subtotal' => $cart->subtotal,
                'discount_total' => $cart->discount_total,
                'tax_total' => $cart->tax_total,
                'shipping_total' => $cart->shipping_total,
                'total' => $cart->total,
                'customer_name' => $payload['customer_name'] ?? null,
                'customer_email' => $payload['customer_email'] ?? null,
                'customer_phone' => $payload['customer_phone'] ?? null,
                'billing_address' => $payload['billing_address'] ?? null,
                'shipping_address' => $payload['shipping_address'] ?? null,
                'customer_note' => $payload['customer_note'] ?? null,
                'internal_note' => $payload['internal_note'] ?? null,
                'idempotency_key' => $idempotencyKey,
                'meta' => $payload['meta'] ?? null,
                'placed_at' => now(),
            ]);

            $order->items()->createMany($cart->items->map(function ($item) use ($order) {
                return [
                    'tenant_id' => $order->tenant_id,
                    'order_id' => $order->getKey(),
                    'product_id' => $item->product_id,
                    'variant_id' => $item->variant_id,
                    'name' => $item->name,
                    'sku' => $item->sku,
                    'quantity' => $item->quantity,
                    'currency' => $item->currency,
                    'unit_price' => $item->unit_price,
                    'line_total' => $item->line_total,
                    'meta' => $item->meta,
                ];
            })->all());

            $order->number = $this->numberGenerator->generate((int) $order->getKey());
            $order->save();

            event(new OrderPlaced($order));

            $this->inventoryService->issueForOrder($order);

            $paymentMethod = $payload['payment_method'] ?? 'wallet';
            if ($paymentMethod === 'wallet') {
                $this->handleWalletPayment($order, $user, $payload);
            } else {
                throw ValidationException::withMessages(['payment_method' => 'روش پرداخت نامعتبر است.']);
            }

            $cart->update(['status' => 'checked_out']);

            return $order->refresh()->loadMissing(['items', 'payments']);
        });
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    protected function handleWalletPayment(Order $order, ?Authenticatable $user, array $payload): void
    {
        if (! $user) {
            throw ValidationException::withMessages(['user' => 'پرداخت کیف پول نیازمند ورود کاربر است.']);
        }

        $tenant = Tenant::query()->find($order->tenant_id);
        if (! $tenant) {
            throw ValidationException::withMessages(['tenant' => 'شناسه تننت نامعتبر است.']);
        }

        $wallet = Wallet::query()
            ->where('tenant_id', $order->tenant_id)
            ->where('user_id', $user->getAuthIdentifier())
            ->where('currency', strtolower($order->currency))
            ->first();

        if (! $wallet) {
            $wallet = $this->walletService->createWallet($user, $tenant, $order->currency);
        }

        $idempotencyKey = $payload['payment_idempotency_key']
            ?? ($order->idempotency_key ? $order->idempotency_key.':payment' : (string) Str::uuid());

        $transaction = $this->walletService->debit($wallet, (float) $order->total, $idempotencyKey, [
            'order_id' => $order->getKey(),
            'number' => $order->number,
            'source' => 'commerce_checkout',
        ]);

        $payment = OrderPayment::query()->create([
            'tenant_id' => $order->tenant_id,
            'order_id' => $order->getKey(),
            'method' => 'wallet',
            'status' => 'captured',
            'currency' => $order->currency,
            'amount' => $order->total,
            'provider' => 'wallet',
            'reference' => (string) $transaction->getKey(),
            'wallet_transaction_id' => $transaction->getKey(),
            'meta' => [
                'idempotency_key' => $idempotencyKey,
            ],
        ]);

        $order->update([
            'payment_status' => 'paid',
            'status' => 'processing',
            'paid_at' => now(),
        ]);

        event(new OrderPaid($order, $payment));
    }
}
