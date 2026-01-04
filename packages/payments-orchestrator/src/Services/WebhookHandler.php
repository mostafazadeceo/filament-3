<?php

namespace Haida\PaymentsOrchestrator\Services;

use Filamat\IamSuite\Support\TenantContext;
use Haida\CommerceOrders\Events\OrderPaid;
use Haida\CommerceOrders\Models\OrderPayment;
use Haida\PaymentsOrchestrator\Models\PaymentGatewayConnection;
use Haida\PaymentsOrchestrator\Models\PaymentIntent;
use Haida\PaymentsOrchestrator\Models\PaymentWebhookEvent;
use Illuminate\Database\DatabaseManager;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class WebhookHandler
{
    public function __construct(
        protected DatabaseManager $db,
        protected GatewayRegistry $registry
    ) {
    }

    public function handle(Request $request, string $providerKey): array
    {
        $tenant = TenantContext::getTenant();
        if (! $tenant) {
            throw new BadRequestHttpException('Tenant not resolved.');
        }

        $connection = PaymentGatewayConnection::query()
            ->where('tenant_id', $tenant->getKey())
            ->where('provider_key', $providerKey)
            ->where('is_active', true)
            ->first();

        if (! $connection) {
            throw new BadRequestHttpException('Gateway connection not found.');
        }

        $payload = $request->getContent();
        $headers = $request->headers->all();

        $adapter = $this->registry->get($providerKey);
        if (! $adapter->verifyWebhook($payload, $headers, $connection)) {
            throw new UnauthorizedHttpException('signature', 'Invalid signature.');
        }

        $event = $adapter->parseWebhook($payload, $headers);
        if (! $event->eventId) {
            throw new BadRequestHttpException('Missing event id.');
        }

        $existing = PaymentWebhookEvent::query()
            ->where('tenant_id', $tenant->getKey())
            ->where('provider_key', $providerKey)
            ->where('event_id', $event->eventId)
            ->first();

        if ($existing) {
            return ['status' => 'duplicate'];
        }

        $webhook = PaymentWebhookEvent::query()->create([
            'tenant_id' => $tenant->getKey(),
            'provider_key' => $providerKey,
            'event_id' => $event->eventId,
            'signature' => $this->resolveSignature($headers),
            'payload' => $payload,
            'headers' => $headers,
            'status' => 'received',
            'idempotency_key' => $event->eventId,
            'received_at' => now(),
        ]);

        try {
            $this->db->transaction(function () use ($event, $providerKey) {
                $intent = null;
                if ($event->intentId) {
                    $intent = PaymentIntent::query()->find($event->intentId);
                }

                if (! $intent && $event->reference) {
                    $intent = PaymentIntent::query()
                        ->where('provider_key', $providerKey)
                        ->where('provider_reference', $event->reference)
                        ->first();
                }

                if (! $intent) {
                    return;
                }

                $intent->update([
                    'status' => $event->status,
                ]);

                $order = $intent->order;
                if (! $order) {
                    return;
                }

                if ($event->status === 'succeeded') {
                    $existingPayment = OrderPayment::query()
                        ->where('order_id', $order->getKey())
                        ->where('provider', $providerKey)
                        ->where('reference', $event->reference)
                        ->first();

                    if (! $existingPayment) {
                        $payment = OrderPayment::query()->create([
                            'tenant_id' => $order->tenant_id,
                            'order_id' => $order->getKey(),
                            'method' => 'gateway',
                            'status' => 'captured',
                            'currency' => $order->currency,
                            'amount' => $order->total,
                            'provider' => $providerKey,
                            'reference' => $event->reference,
                            'meta' => $event->meta,
                        ]);

                        $order->update([
                            'payment_status' => 'paid',
                            'status' => 'processing',
                            'paid_at' => now(),
                        ]);

                        event(new OrderPaid($order, $payment));
                    }
                }

                if ($event->status === 'failed') {
                    $order->update([
                        'payment_status' => 'failed',
                    ]);
                }
            });

            $webhook->update([
                'status' => 'processed',
                'processed_at' => now(),
            ]);
        } catch (\Throwable $exception) {
            $webhook->update([
                'status' => 'failed',
                'processed_at' => now(),
                'error_message' => $exception->getMessage(),
            ]);

            throw $exception;
        }

        return ['status' => 'ok'];
    }

    /**
     * @param  array<string, mixed>  $headers
     */
    private function resolveSignature(array $headers): ?string
    {
        $signatureHeader = (string) config('payments-orchestrator.webhooks.signature_header', 'X-Signature');

        foreach ($headers as $header => $value) {
            if (strcasecmp($header, $signatureHeader) === 0) {
                if (is_array($value)) {
                    return (string) ($value[0] ?? null);
                }

                return (string) $value;
            }
        }

        return null;
    }
}
