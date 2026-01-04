<?php

declare(strict_types=1);

namespace Haida\FilamentCryptoGateway\Jobs;

use Haida\FilamentCryptoGateway\Models\CryptoWebhookCall;
use Haida\FilamentCryptoGateway\Services\WebhookProcessor;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessWebhookCall implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public int $tries = 5;

    public function __construct(public int $webhookCallId) {}

    public function handle(WebhookProcessor $processor): void
    {
        $call = CryptoWebhookCall::query()->find($this->webhookCallId);
        if (! $call) {
            return;
        }

        $processor->process($call);
    }

    public function backoff(): int
    {
        return (int) config('filament-crypto-gateway.webhooks.retry_delay_seconds', 60);
    }
}
