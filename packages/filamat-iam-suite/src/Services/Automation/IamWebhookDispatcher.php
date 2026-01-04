<?php

declare(strict_types=1);

namespace Filamat\IamSuite\Services\Automation;

use Filamat\IamSuite\Contracts\IamEvent;
use Filamat\IamSuite\Models\Webhook;
use Filamat\IamSuite\Models\WebhookDelivery;
use Filamat\IamSuite\Services\WebhookService;

class IamWebhookDispatcher
{
    public function __construct(
        protected WebhookService $webhookService,
        protected IamEventEnvelopeFactory $envelopeFactory,
        protected AutomationRateLimiter $rateLimiter,
    ) {}

    public function dispatch(IamEvent $event): void
    {
        if (! (bool) config('filamat-iam.automation.enabled', true)) {
            return;
        }

        $type = (string) config('filamat-iam.automation.webhook_type', 'automation');

        $query = Webhook::query()
            ->where('type', $type)
            ->where('enabled', true);

        $tenantId = $event->tenantId();
        if (! $tenantId) {
            return;
        }

        $query->where('tenant_id', $tenantId);

        $webhooks = $query->get();
        $eventName = $event->eventName();

        foreach ($webhooks as $webhook) {
            if (is_array($webhook->events) && $webhook->events !== [] && ! in_array($eventName, $webhook->events, true)) {
                continue;
            }

            if (! $this->rateLimiter->allows($webhook, $tenantId)) {
                WebhookDelivery::query()->create([
                    'webhook_id' => $webhook->getKey(),
                    'status' => 'skipped',
                    'idempotency_key' => null,
                    'request' => ['event' => $eventName, 'reason' => 'rate_limited'],
                    'response' => ['status' => 429, 'body' => 'rate_limited'],
                    'attempts' => 0,
                ]);

                continue;
            }

            $payload = $this->envelopeFactory->build($event, $webhook);
            $payload['context'] = array_merge((array) ($payload['context'] ?? []), [
                'connector_id' => $webhook->getKey(),
            ]);

            $this->webhookService->queue($webhook, $payload);
        }
    }
}
