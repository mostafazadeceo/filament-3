<?php

namespace Haida\CommerceOrders\Services;

use Filamat\IamSuite\Services\AuditService;
use Haida\CommerceOrders\Models\Order;
use Illuminate\Database\DatabaseManager;
use Illuminate\Validation\ValidationException;

class OrderWorkflowService
{
    /** @var array<string, array<int, string>> */
    protected array $transitions = [
        'pending' => ['processing', 'cancelled'],
        'processing' => ['fulfilled', 'cancelled'],
        'fulfilled' => ['completed', 'refunded'],
        'completed' => ['refunded'],
        'cancelled' => [],
        'refunded' => [],
    ];

    public function __construct(
        protected DatabaseManager $db,
        protected AuditService $auditService
    ) {
    }

    public function transition(Order $order, string $toStatus, ?string $note = null): Order
    {
        $fromStatus = $order->status;
        if (! $this->canTransition($fromStatus, $toStatus)) {
            throw ValidationException::withMessages([
                'status' => 'انتقال وضعیت سفارش مجاز نیست.',
            ]);
        }

        return $this->db->transaction(function () use ($order, $fromStatus, $toStatus, $note): Order {
            $payload = [
                'status' => $toStatus,
            ];

            if ($toStatus === 'cancelled') {
                $payload['cancelled_at'] = now();
            }

            if ($toStatus === 'fulfilled') {
                $payload['fulfilled_at'] = now();
            }

            if ($toStatus === 'refunded') {
                $payload['payment_status'] = 'refunded';
            }

            if ($note) {
                $payload['internal_note'] = trim($order->internal_note.'\n'.$note);
            }

            $order->update($payload);

            $this->auditService->log('order.status_transition', $order, [
                'from' => $fromStatus,
                'to' => $toStatus,
            ]);

            return $order->refresh();
        });
    }

    protected function canTransition(string $fromStatus, string $toStatus): bool
    {
        if ($fromStatus === $toStatus) {
            return true;
        }

        return in_array($toStatus, $this->transitions[$fromStatus] ?? [], true);
    }
}
