<?php

namespace Haida\FilamentPos\Services;

use Filamat\IamSuite\Support\TenantContext;
use Haida\FilamentPos\Models\PosCashierSession;
use Haida\FilamentPos\Models\PosSale;
use Haida\FilamentPos\Models\PosSaleItem;
use Haida\FilamentPos\Models\PosSalePayment;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\DatabaseManager;
use Illuminate\Validation\ValidationException;

class PosSaleService
{
    public function __construct(protected DatabaseManager $db) {}

    /**
     * @param  array<string, mixed>  $payload
     * @param  array<int, array<string, mixed>>  $items
     * @param  array<int, array<string, mixed>>  $payments
     */
    public function createSale(array $payload, array $items = [], array $payments = [], ?PosCashierSession $session = null, ?Authenticatable $actor = null, bool $offline = false): PosSale
    {
        $tenantId = $payload['tenant_id'] ?? TenantContext::getTenantId();
        if (! $tenantId) {
            throw ValidationException::withMessages(['tenant_id' => 'شناسه تننت الزامی است.']);
        }

        $idempotencyKey = $payload['idempotency_key'] ?? null;
        if ($idempotencyKey) {
            $existing = PosSale::query()
                ->where('tenant_id', $tenantId)
                ->where('idempotency_key', $idempotencyKey)
                ->first();

            if ($existing) {
                return $existing;
            }
        }

        $allowedProviders = (array) config('filament-pos.offline.allowed_payment_providers', ['manual']);
        if ($offline) {
            foreach ($payments as $payment) {
                $provider = $payment['provider'] ?? 'manual';
                if (! in_array($provider, $allowedProviders, true)) {
                    throw ValidationException::withMessages([
                        'payments' => 'پرداخت آفلاین تنها با درگاه‌های مجاز امکان‌پذیر است.',
                    ]);
                }
            }
        }

        return $this->db->transaction(function () use ($payload, $items, $payments, $session, $actor, $tenantId): PosSale {
            $totals = $this->calculateTotals($items);
            $currency = $payload['currency'] ?? config('filament-pos.defaults.currency', 'IRR');

            $sale = PosSale::query()->create([
                'tenant_id' => $tenantId,
                'store_id' => $payload['store_id'] ?? $session?->store_id,
                'register_id' => $payload['register_id'] ?? $session?->register_id,
                'session_id' => $payload['session_id'] ?? $session?->getKey(),
                'device_id' => $payload['device_id'] ?? $session?->device_id,
                'receipt_no' => $payload['receipt_no'] ?? null,
                'status' => $payload['status'] ?? 'open',
                'payment_status' => $payload['payment_status'] ?? 'pending',
                'currency' => $currency,
                'subtotal' => $totals['subtotal'],
                'discount_total' => $totals['discount_total'],
                'tax_total' => $totals['tax_total'],
                'total' => $totals['total'],
                'source' => $payload['source'] ?? 'pos',
                'idempotency_key' => $payload['idempotency_key'] ?? null,
                'created_by_user_id' => $actor?->getAuthIdentifier(),
                'completed_at' => $payload['completed_at'] ?? null,
                'metadata' => $payload['metadata'] ?? null,
            ]);

            foreach ($items as $item) {
                PosSaleItem::query()->create([
                    'tenant_id' => $tenantId,
                    'sale_id' => $sale->getKey(),
                    'product_id' => $item['product_id'] ?? null,
                    'variant_id' => $item['variant_id'] ?? null,
                    'name' => $item['name'] ?? 'Item',
                    'sku' => $item['sku'] ?? null,
                    'barcode' => $item['barcode'] ?? null,
                    'quantity' => $item['quantity'] ?? 1,
                    'unit_price' => $item['unit_price'] ?? 0,
                    'discount_amount' => $item['discount_amount'] ?? 0,
                    'tax_amount' => $item['tax_amount'] ?? 0,
                    'total' => $item['total'] ?? 0,
                    'metadata' => $item['metadata'] ?? null,
                ]);
            }

            $paymentTotal = 0.0;
            foreach ($payments as $payment) {
                $amount = (float) ($payment['amount'] ?? 0);
                $paymentTotal += $amount;

                PosSalePayment::query()->create([
                    'tenant_id' => $tenantId,
                    'sale_id' => $sale->getKey(),
                    'provider' => $payment['provider'] ?? 'manual',
                    'amount' => $amount,
                    'currency' => $payment['currency'] ?? $currency,
                    'status' => $payment['status'] ?? 'pending',
                    'reference' => $payment['reference'] ?? null,
                    'processed_at' => $payment['processed_at'] ?? null,
                    'metadata' => $payment['metadata'] ?? null,
                ]);
            }

            $paymentStatus = $sale->payment_status;
            if ($paymentTotal >= (float) $sale->total && $sale->total > 0) {
                $paymentStatus = 'paid';
                $sale->status = $sale->status === 'open' ? 'paid' : $sale->status;
                $sale->completed_at = $sale->completed_at ?? now();
            } elseif ($paymentTotal > 0) {
                $paymentStatus = 'partial';
            }

            $sale->payment_status = $paymentStatus;
            $sale->save();

            return $sale->refresh();
        });
    }

    /**
     * @param  array<int, array<string, mixed>>  $items
     * @return array<string, float>
     */
    protected function calculateTotals(array $items): array
    {
        $subtotal = 0.0;
        $discountTotal = 0.0;
        $taxTotal = 0.0;

        foreach ($items as $item) {
            $quantity = (float) ($item['quantity'] ?? 1);
            $unitPrice = (float) ($item['unit_price'] ?? 0);
            $lineSubtotal = $quantity * $unitPrice;

            $discount = (float) ($item['discount_amount'] ?? 0);
            $tax = (float) ($item['tax_amount'] ?? 0);

            $subtotal += $lineSubtotal;
            $discountTotal += $discount;
            $taxTotal += $tax;
        }

        $total = $subtotal - $discountTotal + $taxTotal;

        return [
            'subtotal' => $subtotal,
            'discount_total' => $discountTotal,
            'tax_total' => $taxTotal,
            'total' => $total,
        ];
    }
}
