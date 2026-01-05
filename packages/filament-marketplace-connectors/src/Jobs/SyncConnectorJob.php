<?php

namespace Haida\FilamentMarketplaceConnectors\Jobs;

use Haida\FilamentMarketplaceConnectors\Models\MarketplaceConnector;
use Haida\FilamentMarketplaceConnectors\Models\MarketplaceSyncJob;
use Haida\FilamentMarketplaceConnectors\Services\MarketplaceConnectorRegistry;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SyncConnectorJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(
        protected MarketplaceSyncJob $syncJob
    ) {}

    public function handle(MarketplaceConnectorRegistry $registry): void
    {
        $connector = MarketplaceConnector::query()->find($this->syncJob->connector_id);
        if (! $connector) {
            $this->syncJob->update([
                'status' => 'failed',
                'error' => 'connector_not_found',
                'last_run_at' => now(),
            ]);

            return;
        }

        $handler = $registry->get($connector->provider_key);

        $result = match ($this->syncJob->job_type) {
            'catalog' => $handler->syncCatalog($connector),
            'inventory' => $handler->syncInventory($connector),
            'orders' => $handler->syncOrders($connector),
            default => ['status' => 'unknown_job'],
        };

        $status = $result['status'] ?? 'completed';

        $this->syncJob->update([
            'status' => $status,
            'last_run_at' => now(),
            'metadata' => $result,
        ]);
    }
}
