<?php

namespace Haida\FilamentPos\Services;

use Filamat\IamSuite\Support\TenantContext;
use Haida\FilamentPos\Models\PosCashierSession;
use Haida\FilamentPos\Models\PosDevice;
use Haida\FilamentPos\Models\PosOutbox;
use Haida\FilamentPos\Models\PosRegister;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\DatabaseManager;
use Illuminate\Validation\ValidationException;

class PosOutboxService
{
    public function __construct(
        protected DatabaseManager $db,
        protected PosCashierSessionService $sessionService,
        protected PosSaleService $saleService
    ) {}

    /**
     * @param  array<int, array<string, mixed>>  $events
     * @return array{accepted: array<int, array<string, mixed>>, rejected: array<int, array<string, mixed>>}
     */
    public function processEvents(array $events, ?PosDevice $device = null, ?Authenticatable $actor = null): array
    {
        $accepted = [];
        $rejected = [];
        $tenantId = TenantContext::getTenantId();
        if (! $tenantId) {
            throw ValidationException::withMessages(['tenant_id' => 'شناسه تننت الزامی است.']);
        }

        foreach ($events as $event) {
            $eventType = $event['event_type'] ?? null;
            $payload = $event['payload'] ?? [];
            $idempotencyKey = $event['idempotency_key'] ?? null;
            if ($idempotencyKey && ! array_key_exists('idempotency_key', $payload)) {
                $payload['idempotency_key'] = $idempotencyKey;
            }

            if (! $eventType) {
                $rejected[] = [
                    'event_type' => null,
                    'reason' => 'missing_event_type',
                ];

                continue;
            }

            if ($idempotencyKey) {
                $existing = PosOutbox::query()
                    ->where('tenant_id', $tenantId)
                    ->where('idempotency_key', $idempotencyKey)
                    ->first();

                if ($existing) {
                    $accepted[] = [
                        'event_type' => $eventType,
                        'status' => 'duplicate',
                        'outbox_id' => $existing->getKey(),
                    ];

                    continue;
                }
            }

            $outbox = PosOutbox::query()->create([
                'tenant_id' => $tenantId,
                'device_id' => $device?->getKey(),
                'event_type' => $eventType,
                'event_id' => $event['event_id'] ?? null,
                'idempotency_key' => $idempotencyKey,
                'status' => 'pending',
                'payload' => $payload,
            ]);

            try {
                $this->applyEvent($eventType, $payload, $device, $actor, $idempotencyKey);

                $outbox->update([
                    'status' => 'processed',
                    'processed_at' => now(),
                ]);

                $accepted[] = [
                    'event_type' => $eventType,
                    'status' => 'processed',
                    'outbox_id' => $outbox->getKey(),
                ];
            } catch (ValidationException $exception) {
                $outbox->update([
                    'status' => 'rejected',
                    'error_reason' => json_encode($exception->errors(), JSON_UNESCAPED_UNICODE),
                    'processed_at' => now(),
                ]);

                $rejected[] = [
                    'event_type' => $eventType,
                    'reason' => 'validation_failed',
                    'errors' => $exception->errors(),
                ];
            } catch (\Throwable $exception) {
                $outbox->update([
                    'status' => 'rejected',
                    'error_reason' => $exception->getMessage(),
                    'processed_at' => now(),
                ]);

                $rejected[] = [
                    'event_type' => $eventType,
                    'reason' => 'processing_error',
                ];
            }
        }

        return [
            'accepted' => $accepted,
            'rejected' => $rejected,
        ];
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    protected function applyEvent(string $eventType, array $payload, ?PosDevice $device, ?Authenticatable $actor, ?string $idempotencyKey = null): void
    {
        $tenantId = TenantContext::getTenantId();

        if ($eventType === 'session_open') {
            $register = PosRegister::query()->findOrFail($payload['register_id'] ?? null);
            $this->sessionService->openSession(
                $register,
                (float) ($payload['opening_float'] ?? 0),
                $device?->getKey(),
                $actor
            );

            return;
        }

        if ($eventType === 'session_close') {
            $session = PosCashierSession::query()->findOrFail($payload['session_id'] ?? null);
            $this->sessionService->closeSession(
                $session,
                (float) ($payload['closing_cash'] ?? 0),
                $actor
            );

            return;
        }

        if ($eventType === 'cash_movement') {
            $session = PosCashierSession::query()->findOrFail($payload['session_id'] ?? null);
            $this->sessionService->recordMovement(
                $session,
                (string) ($payload['type'] ?? 'pay_in'),
                (float) ($payload['amount'] ?? 0),
                $payload['reason'] ?? null,
                $actor
            );

            return;
        }

        if ($eventType === 'sale') {
            $items = $payload['items'] ?? [];
            $payments = $payload['payments'] ?? [];
            $session = null;
            if (! empty($payload['session_id'])) {
                $session = PosCashierSession::query()->find($payload['session_id']);
            }

            $payload['tenant_id'] = $tenantId;
            $payload['device_id'] = $payload['device_id'] ?? $device?->getKey();
            $payload['idempotency_key'] = $payload['idempotency_key'] ?? $idempotencyKey;

            $this->saleService->createSale($payload, $items, $payments, $session, $actor, true);

            return;
        }

        throw ValidationException::withMessages([
            'event_type' => 'نوع رویداد ناشناخته است.',
        ]);
    }
}
