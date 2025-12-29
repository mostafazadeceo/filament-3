<?php

namespace Vendor\FilamentAccountingIr\Services\Webhooks;

use Filamat\IamSuite\Models\Webhook;
use Filamat\IamSuite\Services\WebhookService;
use Vendor\FilamentAccountingIr\Contracts\AccountingEvent;

class AccountingWebhookDispatcher
{
    public function __construct(protected WebhookService $webhookService) {}

    public function dispatch(AccountingEvent $event): void
    {
        $query = Webhook::query()
            ->where('type', 'accounting')
            ->where('enabled', true);

        $tenantId = $event->tenantId();
        if ($tenantId) {
            $query->where(function ($builder) use ($tenantId) {
                $builder->whereNull('tenant_id')->orWhere('tenant_id', $tenantId);
            });
        }

        $webhooks = $query->get();
        $payload = $event->payload();
        $eventName = $event->eventName();

        foreach ($webhooks as $webhook) {
            if (is_array($webhook->events) && $webhook->events !== [] && ! in_array($eventName, $webhook->events, true)) {
                continue;
            }

            $this->webhookService->queue($webhook, $payload);
        }
    }
}
