<?php

declare(strict_types=1);

namespace Filamat\IamSuite\Jobs;

use Filamat\IamSuite\Models\WebhookDelivery;
use Filamat\IamSuite\Services\WebhookService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class DeliverWebhookJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public int $tries = 3;

    public function __construct(public int $deliveryId) {}

    public function backoff(): array
    {
        return [10, 30, 60, 120];
    }

    public function handle(WebhookService $service): void
    {
        $delivery = WebhookDelivery::query()->find($this->deliveryId);
        if (! $delivery) {
            return;
        }

        $service->deliver($delivery);
    }
}
