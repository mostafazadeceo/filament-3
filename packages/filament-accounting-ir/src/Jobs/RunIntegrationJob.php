<?php

namespace Vendor\FilamentAccountingIr\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Vendor\FilamentAccountingIr\Models\IntegrationConnector;
use Vendor\FilamentAccountingIr\Services\Integration\IntegrationRunner;

class RunIntegrationJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(public int $connectorId) {}

    public function handle(IntegrationRunner $runner): void
    {
        $connector = IntegrationConnector::query()->find($this->connectorId);
        if (! $connector || ! $connector->is_active) {
            return;
        }

        $runner->run($connector);
    }
}
