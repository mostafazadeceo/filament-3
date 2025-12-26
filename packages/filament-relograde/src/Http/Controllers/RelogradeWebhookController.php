<?php

namespace Haida\FilamentRelograde\Http\Controllers;

use Haida\FilamentRelograde\Enums\Environment;
use Haida\FilamentRelograde\Jobs\ProcessWebhookEventJob;
use Haida\FilamentRelograde\Models\RelogradeConnection;
use Haida\FilamentRelograde\Models\RelogradeWebhookEvent;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class RelogradeWebhookController
{
    public function handle(Request $request): Response
    {
        if (! $request->isJson()) {
            return response(['message' => 'نوع محتوا نامعتبر است.'], 415);
        }

        $payload = $request->json()->all();
        if (($payload['event'] ?? null) !== 'ORDER_FINISHED') {
            return response(['message' => 'نادیده گرفته شد.'], 202);
        }

        $connection = $this->resolveConnection($payload);
        if (! $this->ipAllowed($request->ip(), $connection)) {
            return response(['message' => 'دسترسی غیرمجاز.'], 403);
        }

        if ($connection && $connection->webhook_secret) {
            $headerName = (string) config('relograde.webhooks.secret_header', 'X-Relograde-Secret');
            $secret = $request->header($headerName);

            if (! $secret || ! hash_equals($connection->webhook_secret, $secret)) {
                return response(['message' => 'دسترسی غیرمجاز.'], 403);
            }
        }

        $event = RelogradeWebhookEvent::create([
            'connection_id' => $connection?->getKey(),
            'event' => $payload['event'] ?? null,
            'state' => $payload['state'] ?? null,
            'api_key_description' => $payload['apiKeyDescription'] ?? null,
            'trx' => data_get($payload, 'data.trx'),
            'reference' => data_get($payload, 'data.reference'),
            'payload' => $payload,
            'received_ip' => $request->ip(),
            'processing_status' => 'pending',
        ]);

        ProcessWebhookEventJob::dispatch($event->getKey());

        return response(['status' => 'موفق'], 200);
    }

    protected function resolveConnection(array $payload): ?RelogradeConnection
    {
        $state = $payload['state'] ?? null;
        $description = $payload['apiKeyDescription'] ?? null;
        $environment = Environment::fromWebhookState($state);

        $query = RelogradeConnection::query();
        if ($environment) {
            $query->where('environment', $environment->value);
        }

        if ($description) {
            $query->where('name', $description);
        }

        return $query->first() ?? RelogradeConnection::query()->default($environment?->value)->first();
    }

    protected function ipAllowed(?string $ip, ?RelogradeConnection $connection): bool
    {
        if (! $ip) {
            return false;
        }

        $allowed = $connection?->allowedWebhookIps() ?? (array) config('relograde.webhooks.allowed_ips', []);

        return in_array($ip, $allowed, true);
    }
}
