<?php

namespace Haida\FilamentRelograde\Jobs;

use Haida\FilamentRelograde\Clients\RelogradeClientFactory;
use Haida\FilamentRelograde\Enums\Environment;
use Haida\FilamentRelograde\Models\RelogradeConnection;
use Haida\FilamentRelograde\Models\RelogradeWebhookEvent;
use Haida\FilamentRelograde\Services\RelogradeOrderSynchronizer;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Throwable;

class ProcessWebhookEventJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(
        public int $webhookEventId,
    ) {}

    public function handle(RelogradeClientFactory $clientFactory, RelogradeOrderSynchronizer $synchronizer): void
    {
        $event = RelogradeWebhookEvent::find($this->webhookEventId);
        if (! $event) {
            return;
        }

        try {
            $connection = $event->connection ?: $this->resolveConnection($event);
            if (! $connection) {
                $event->processing_status = 'failed';
                $event->error_message = 'اتصال پیدا نشد.';
                $event->processed_at = now();
                $event->save();

                return;
            }

            $trx = $event->trx ?? data_get($event->payload, 'data.trx');
            if (! $trx) {
                $event->processing_status = 'failed';
                $event->error_message = 'شناسه تراکنش موجود نیست.';
                $event->processed_at = now();
                $event->save();

                return;
            }

            $client = $clientFactory->make($connection);
            $payload = $client->findOrder($trx);
            $synchronizer->sync($connection, $payload);

            $event->processing_status = 'processed';
            $event->processed_at = now();
            $event->save();
        } catch (Throwable $exception) {
            $event->processing_status = 'failed';
            $event->error_message = $exception->getMessage();
            $event->processed_at = now();
            $event->save();
        }
    }

    protected function resolveConnection(RelogradeWebhookEvent $event): ?RelogradeConnection
    {
        $state = $event->state ?? data_get($event->payload, 'state');
        $description = $event->api_key_description ?? data_get($event->payload, 'apiKeyDescription');

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
}
