<?php

declare(strict_types=1);

namespace Haida\ProvidersEsimGoWebhooks\Http\Controllers;

use Haida\ProvidersCore\DataTransferObjects\ProviderContext;
use Haida\ProvidersCore\Services\ProviderJobDispatcher;
use Haida\ProvidersCore\Support\ProviderAction;
use Haida\ProvidersEsimGoCore\Models\EsimGoConnection;
use Haida\ProvidersEsimGoCore\Services\EsimGoWebhookService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class EsimGoWebhookController
{
    public function __construct(
        protected ProviderJobDispatcher $dispatcher,
        protected EsimGoWebhookService $webhookService,
    ) {}

    public function handle(Request $request): Response
    {
        $rawBody = (string) $request->getContent();
        $payload = json_decode($rawBody, true);

        if (! is_array($payload)) {
            return response(['message' => trans('providers-esim-go-webhooks::messages.invalid_payload')], 422);
        }

        $connection = $this->resolveConnection($request, $payload);
        if (! $connection) {
            return response(['message' => trans('providers-esim-go-webhooks::messages.connection_not_found')], 404);
        }

        $signature = $this->resolveSignature($request);
        $signatureValid = $signature !== null && $this->verifySignature($connection->api_key, $rawBody, $signature);

        if (! $signatureValid) {
            return response(['message' => trans('providers-esim-go-webhooks::messages.invalid_signature')], 403);
        }

        $eventType = $this->webhookService->resolveEventType($payload);
        if ($this->webhookService->isLocationEvent($eventType)) {
            return response(['status' => 'ignored'], 200);
        }

        $context = new ProviderContext($connection->tenant_id, $connection->getKey(), (bool) ($connection->metadata['sandbox'] ?? false));
        $this->dispatcher->dispatch(ProviderAction::HandleWebhook, $context, 'esim-go', [
            'raw_body' => $rawBody,
            'signature_valid' => $signatureValid,
            'payload' => $payload,
            'received_ip' => $request->ip(),
        ]);

        return response(['status' => 'ok'], 200);
    }

    protected function resolveConnection(Request $request, array $payload): ?EsimGoConnection
    {
        $resolveBy = (string) config('providers-esim-go-core.webhooks.resolve_connection_by', 'connection_id');

        $connectionIdParam = (string) config('providers-esim-go-core.webhooks.connection_id_param', 'connection_id');
        $tenantIdParam = (string) config('providers-esim-go-core.webhooks.tenant_id_param', 'tenant_id');

        if ($resolveBy === 'connection_id') {
            $connectionId = $request->query($connectionIdParam);
            if ($connectionId) {
                return EsimGoConnection::query()->find($connectionId);
            }
        }

        if ($resolveBy === 'tenant_id') {
            $tenantId = $request->query($tenantIdParam);
            if ($tenantId) {
                return EsimGoConnection::query()->where('tenant_id', $tenantId)->default()->first();
            }
        }

        $tenantId = $request->query($tenantIdParam);
        if ($tenantId) {
            return EsimGoConnection::query()->where('tenant_id', $tenantId)->default()->first();
        }

        return null;
    }

    protected function resolveSignature(Request $request): ?string
    {
        $headers = (array) config('providers-esim-go-core.signature_headers', []);
        foreach ($headers as $header) {
            $value = $request->header($header);
            if ($value) {
                return (string) $value;
            }
        }

        return null;
    }

    protected function verifySignature(string $apiKey, string $rawBody, string $signature): bool
    {
        $expected = base64_encode(hash_hmac('sha256', $rawBody, $apiKey, true));

        return hash_equals($expected, $signature);
    }
}
