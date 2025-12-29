<?php

namespace Vendor\FilamentAccountingIr\Console\Commands;

use Illuminate\Console\Command;
use Vendor\FilamentAccountingIr\Jobs\RunIntegrationJob;
use Vendor\FilamentAccountingIr\Models\IntegrationConnector;

class RunAccountingIntegrations extends Command
{
    protected $signature = 'accounting-ir:run-integrations {--sync : Run synchronously}';

    protected $description = 'Run active accounting integrations.';

    public function handle(): int
    {
        $sync = (bool) $this->option('sync');
        $connectors = IntegrationConnector::query()->where('is_active', true)->get();

        foreach ($connectors as $connector) {
            if ($sync) {
                RunIntegrationJob::dispatchSync($connector->getKey());
            } else {
                RunIntegrationJob::dispatch($connector->getKey());
            }
        }

        $this->info('Integration jobs queued.');

        return self::SUCCESS;
    }
}
