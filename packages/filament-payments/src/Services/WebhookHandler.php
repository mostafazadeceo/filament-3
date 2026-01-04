<?php

namespace Haida\FilamentPayments\Services;

use Filamat\IamSuite\Models\Tenant;
use Filamat\IamSuite\Support\TenantContext;
use Haida\FilamentPayments\Models\PaymentProviderConnection;
use Haida\FilamentPayments\Models\PaymentWebhookEvent;
use Haida\FilamentPayments\Support\WebhookSignature;
use Illuminate\Support\Arr;

class WebhookHandler
{
    public function __construct(
        protected PaymentProviderRegistry $registry,
        protected WebhookSignature $signatureVerifier
    ) {}

    /**
     * @param  array<string, mixed>  $headers
     * @param  array<string, mixed>  $payload
     */
    public function handle(string $providerKey, array $headers, array $payload, ?int $tenantId = null, ?string $rawPayload = null): PaymentWebhookEvent
    {
        $previousTenant = TenantContext::getTenant();
        $tenant = $tenantId ? Tenant::query()->find($tenantId) : null;
        if ($tenant) {
            TenantContext::setTenant($tenant);
        }

        try {
            $provider = $this->registry->get($providerKey);

            $payloadJson = $rawPayload ?? json_encode($payload);
            $externalId = (string) (Arr::get($payload, 'id') ?? Arr::get($payload, 'event_id') ?? sha1((string) $payloadJson));

            $event = PaymentWebhookEvent::query()->firstOrCreate([
                'provider' => $providerKey,
                'external_id' => $externalId,
            ], [
                'tenant_id' => $tenantId,
                'event_type' => Arr::get($payload, 'type'),
                'headers' => $this->sanitizeHeaders($headers),
                'payload' => $payload,
                'received_at' => now(),
                'status' => 'received',
                'signature_valid' => false,
            ]);

            if ($event->processed_at) {
                return $event;
            }

            $connection = null;
            if ($tenantId) {
                $connection = PaymentProviderConnection::query()
                    ->where('tenant_id', $tenantId)
                    ->where('provider_key', $providerKey)
                    ->where('is_active', true)
                    ->first();
            }

            $signatureValid = false;
            if ($connection && is_array($connection->credentials)) {
                $secret = $connection->credentials['webhook_secret'] ?? $connection->credentials['secret'] ?? null;
                if ($secret && $payloadJson) {
                    $signatureValid = $this->signatureVerifier->verify($payloadJson, $headers, $secret);
                }
            }

            if (! $signatureValid) {
                $event->update([
                    'status' => 'invalid_signature',
                    'signature_valid' => false,
                    'processed_at' => now(),
                ]);

                return $event->refresh();
            }

            $result = $provider->handleWebhook($headers, $payload, $connection ?? new PaymentProviderConnection);

            $event->update([
                'status' => $result->status,
                'signature_valid' => $signatureValid,
                'processed_at' => now(),
            ]);

            return $event->refresh();
        } finally {
            if ($tenant) {
                TenantContext::setTenant($previousTenant);
            }
        }
    }

    /**
     * @param  array<string, mixed>  $headers
     * @return array<string, mixed>
     */
    private function sanitizeHeaders(array $headers): array
    {
        $blocked = [
            'authorization',
            'x-api-key',
            'api-key',
            'x-signature',
            'signature',
            'cookie',
            'set-cookie',
        ];

        $clean = [];
        foreach ($headers as $key => $value) {
            if (in_array(strtolower((string) $key), $blocked, true)) {
                continue;
            }

            $clean[$key] = $value;
        }

        return $clean;
    }
}
