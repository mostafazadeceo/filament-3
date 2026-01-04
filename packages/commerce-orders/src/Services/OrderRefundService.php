<?php

namespace Haida\CommerceOrders\Services;

use Filamat\IamSuite\Services\AuditService;
use Haida\FilamentCommerceCore\Services\CommerceComplianceService;
use Haida\CommerceOrders\Models\Order;
use Haida\CommerceOrders\Models\OrderPayment;
use Haida\CommerceOrders\Models\OrderRefund;
use Haida\CommerceOrders\Models\OrderReturn;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\DatabaseManager;

class OrderRefundService
{
    public function __construct(
        protected DatabaseManager $db,
        protected AuditService $auditService
    ) {
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    public function createRefund(
        Order $order,
        float $amount,
        array $payload = [],
        ?OrderReturn $return = null,
        ?OrderPayment $payment = null,
        ?Authenticatable $actor = null
    ): OrderRefund {
        return $this->db->transaction(function () use ($order, $amount, $payload, $return, $payment, $actor): OrderRefund {
            $idempotencyKey = $payload['idempotency_key'] ?? null;
            if ($idempotencyKey) {
                $existing = OrderRefund::query()
                    ->where('tenant_id', $order->tenant_id)
                    ->where('idempotency_key', $idempotencyKey)
                    ->first();

                if ($existing) {
                    return $existing;
                }
            }

            $refund = OrderRefund::query()->create([
                'tenant_id' => $order->tenant_id,
                'order_id' => $order->getKey(),
                'order_return_id' => $return?->getKey(),
                'order_payment_id' => $payment?->getKey(),
                'status' => $payload['status'] ?? 'pending',
                'currency' => $payload['currency'] ?? $order->currency,
                'amount' => $amount,
                'provider' => $payload['provider'] ?? null,
                'reference' => $payload['reference'] ?? null,
                'reason' => $payload['reason'] ?? null,
                'idempotency_key' => $idempotencyKey,
                'notes' => $payload['notes'] ?? null,
                'processed_at' => $payload['processed_at'] ?? null,
                'created_by_user_id' => $actor?->getAuthIdentifier(),
                'meta' => $payload['meta'] ?? null,
            ]);

            $totalRefunded = (float) OrderRefund::query()
                ->where('order_id', $order->getKey())
                ->sum('amount');

            $newPaymentStatus = $totalRefunded >= (float) $order->total
                ? 'refunded'
                : 'partially_refunded';

            $order->update([
                'payment_status' => $newPaymentStatus,
                'status' => $newPaymentStatus === 'refunded' ? 'refunded' : $order->status,
            ]);

            $this->auditService->log('order.refund.created', $refund, [
                'amount' => $amount,
            ], $actor);

            if (class_exists(CommerceComplianceService::class)) {
                try {
                    app(CommerceComplianceService::class)->evaluate('refund', [
                        'tenant_id' => $order->tenant_id,
                        'amount' => $amount,
                        'currency' => $refund->currency,
                        'order_id' => $order->getKey(),
                        'idempotency_key' => $idempotencyKey,
                    ], $refund, $actor);
                } catch (\Throwable) {
                    // Ignore compliance failures to avoid breaking refunds.
                }
            }

            return $refund;
        });
    }
}
