<?php

declare(strict_types=1);

namespace Haida\ProvidersEsimGoCore\Services;

use Haida\FilamentNotify\Core\Support\Triggers\TriggerDispatcher;
use Haida\ProvidersEsimGoCore\Models\EsimGoCallback;
use Haida\ProvidersEsimGoCore\Models\EsimGoConnection;
use Haida\ProvidersEsimGoCore\Models\EsimGoEsim;
use Haida\ProvidersEsimGoCore\Models\EsimGoInventoryUsage;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class EsimGoWebhookService
{
    /**
     * @param  array<string, mixed>  $payload
     */
    public function ingest(
        EsimGoConnection $connection,
        string $rawBody,
        array $payload,
        bool $signatureValid,
        ?string $receivedIp = null,
    ): ?EsimGoCallback {
        $eventType = $this->resolveEventType($payload);
        if ($this->isLocationEvent($eventType)) {
            return null;
        }

        $payloadHash = sha1($rawBody);
        $existing = EsimGoCallback::query()
            ->where('tenant_id', $connection->tenant_id)
            ->where('payload_hash', $payloadHash)
            ->first();

        if ($existing) {
            return $existing;
        }

        $iccid = (string) ($this->resolveIccid($payload) ?? '');
        $bundleRef = (string) ($this->resolveBundleRef($payload) ?? '');
        $remaining = $this->resolveRemaining($payload);
        $correlationId = app()->bound('correlation_id') ? app('correlation_id') : null;

        $callback = EsimGoCallback::query()->create([
            'tenant_id' => $connection->tenant_id,
            'event_type' => $eventType,
            'iccid' => $iccid !== '' ? $iccid : null,
            'bundle_ref' => $bundleRef !== '' ? $bundleRef : null,
            'remaining_quantity' => $remaining,
            'payload_hash' => $payloadHash,
            'raw_body' => $rawBody,
            'payload' => $payload,
            'signature_valid' => $signatureValid,
            'correlation_id' => is_string($correlationId) ? $correlationId : null,
            'received_at' => now(),
        ]);

        $panelId = (string) config('providers-esim-go-core.notifications.panel', 'tenant');
        $event = (string) config('providers-esim-go-core.notifications.callback_event', 'webhook_received');
        if ($panelId !== '' && class_exists(TriggerDispatcher::class)) {
            try {
                app(TriggerDispatcher::class)->dispatchForEloquent($panelId, $callback, $event);
            } catch (\Throwable) {
                // Ignore notify failures to keep webhook processing resilient.
            }
        }

        if ($iccid !== '') {
            EsimGoEsim::query()->where('tenant_id', $connection->tenant_id)
                ->where('iccid', $iccid)
                ->update([
                    'state' => (string) data_get($payload, 'state', data_get($payload, 'status', 'active')),
                    'last_refreshed_at' => now(),
                ]);
        }

        $usageId = $this->resolveUsageId($payload);
        if ($usageId !== null) {
            EsimGoInventoryUsage::query()->updateOrCreate([
                'tenant_id' => $connection->tenant_id,
                'usage_id' => (string) $usageId,
            ], [
                'bundle_name' => $bundleRef !== '' ? $bundleRef : data_get($payload, 'bundle.name'),
                'remaining' => $remaining,
                'expiry_at' => data_get($payload, 'expiryAt', data_get($payload, 'expiry')),
                'countries' => Arr::wrap(data_get($payload, 'countries')),
                'fetched_at' => now(),
            ]);
        }

        return $callback;
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    public function resolveEventType(array $payload): string
    {
        $candidate = data_get($payload, 'eventType')
            ?? data_get($payload, 'event')
            ?? data_get($payload, 'type')
            ?? data_get($payload, 'alertType')
            ?? 'unknown';

        return (string) $candidate;
    }

    public function isLocationEvent(string $eventType): bool
    {
        $normalized = Str::lower($eventType);

        return str_contains($normalized, 'location') || str_contains($normalized, 'position');
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    protected function resolveIccid(array $payload): ?string
    {
        return data_get($payload, 'iccid')
            ?? data_get($payload, 'sim.iccid')
            ?? data_get($payload, 'esim.iccid');
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    protected function resolveBundleRef(array $payload): ?string
    {
        return data_get($payload, 'bundle.reference')
            ?? data_get($payload, 'bundleRef')
            ?? data_get($payload, 'bundle.name');
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    protected function resolveRemaining(array $payload): ?float
    {
        $remaining = data_get($payload, 'remainingQuantity', data_get($payload, 'remaining'));
        if ($remaining === null || $remaining === '') {
            return null;
        }

        return (float) $remaining;
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    protected function resolveUsageId(array $payload): ?string
    {
        $usageId = data_get($payload, 'usageId', data_get($payload, 'usage_id', data_get($payload, 'usage')));
        if (! $usageId) {
            return null;
        }

        return (string) $usageId;
    }
}
