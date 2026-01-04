<?php

namespace Haida\FilamentPayments\Services;

use Filamat\IamSuite\Support\TenantContext;
use Haida\FilamentPayments\Models\PaymentIntent;
use Haida\FilamentPayments\Models\PaymentProviderConnection;
use Illuminate\Database\DatabaseManager;
use Illuminate\Validation\ValidationException;

class PaymentIntentService
{
    public function __construct(
        protected DatabaseManager $db,
        protected PaymentProviderRegistry $registry
    ) {}

    /**
     * @param  array<string, mixed>  $data
     * @param  array<string, mixed>  $payload
     */
    public function createIntent(array $data, array $payload = []): PaymentIntent
    {
        $tenantId = $data['tenant_id'] ?? TenantContext::getTenantId();
        if (! $tenantId) {
            throw ValidationException::withMessages(['tenant_id' => 'شناسه تننت الزامی است.']);
        }

        $idempotencyKey = $data['idempotency_key'] ?? null;
        if ($idempotencyKey) {
            $existing = PaymentIntent::query()
                ->where('tenant_id', $tenantId)
                ->where('idempotency_key', $idempotencyKey)
                ->first();

            if ($existing) {
                return $existing;
            }
        }

        $providerKey = $data['provider'] ?? 'manual';
        $provider = $this->registry->get($providerKey);

        return $this->db->transaction(function () use ($data, $payload, $tenantId, $provider, $providerKey): PaymentIntent {
            $intent = PaymentIntent::query()->create([
                'tenant_id' => $tenantId,
                'reference_type' => $data['reference_type'] ?? null,
                'reference_id' => $data['reference_id'] ?? null,
                'status' => $data['status'] ?? 'pending',
                'provider' => $providerKey,
                'currency' => $data['currency'] ?? 'IRR',
                'amount' => $data['amount'] ?? 0,
                'idempotency_key' => $data['idempotency_key'] ?? null,
                'expires_at' => $data['expires_at'] ?? null,
                'created_by_user_id' => $data['created_by_user_id'] ?? null,
                'metadata' => $data['metadata'] ?? null,
            ]);

            $connection = PaymentProviderConnection::query()
                ->where('tenant_id', $tenantId)
                ->where('provider_key', $providerKey)
                ->where('is_active', true)
                ->first();

            $response = $provider->createIntent($intent, array_merge($payload, [
                'connection' => $connection,
            ]));

            $intent->update([
                'provider_reference' => $response->providerReference,
                'redirect_url' => $response->redirectUrl,
                'status' => $response->status ?: $intent->status,
                'metadata' => array_merge((array) $intent->metadata, $response->meta),
            ]);

            return $intent->refresh();
        });
    }

    public function markConfirmed(PaymentIntent $intent, array $meta = []): PaymentIntent
    {
        $intent->update([
            'status' => 'confirmed',
            'confirmed_at' => now(),
            'metadata' => array_merge((array) $intent->metadata, $meta),
        ]);

        return $intent->refresh();
    }

    public function markFailed(PaymentIntent $intent, array $meta = []): PaymentIntent
    {
        $intent->update([
            'status' => 'failed',
            'failed_at' => now(),
            'metadata' => array_merge((array) $intent->metadata, $meta),
        ]);

        return $intent->refresh();
    }
}
