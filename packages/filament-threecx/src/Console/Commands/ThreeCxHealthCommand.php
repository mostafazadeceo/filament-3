<?php

namespace Haida\FilamentThreeCx\Console\Commands;

use Filamat\IamSuite\Support\TenantContext;
use Haida\FilamentThreeCx\Clients\XapiClient;
use Haida\FilamentThreeCx\Models\ThreeCxInstance;
use Haida\FilamentThreeCx\Services\ThreeCxEventDispatcher;
use Illuminate\Console\Command;

class ThreeCxHealthCommand extends Command
{
    protected $signature = 'threecx:health {instance?}';

    protected $description = 'Check 3CX instance health status.';

    public function handle(ThreeCxEventDispatcher $events): int
    {
        $instanceArg = $this->argument('instance');
        $instances = $instanceArg
            ? ThreeCxInstance::query()->where('id', $instanceArg)->get()
            : ThreeCxInstance::query()->get();

        if ($instances->isEmpty()) {
            $this->warn('هیچ اتصالی یافت نشد.');

            return self::SUCCESS;
        }

        foreach ($instances as $instance) {
            if (! $instance->xapi_enabled) {
                $this->warn("XAPI برای اتصال {$instance->getKey()} غیرفعال است.");

                continue;
            }

            $previousTenant = TenantContext::getTenant();

            try {
                TenantContext::setTenant($instance->tenant);
                $client = app(XapiClient::class, ['instance' => $instance]);
                $payload = $client->health();

                $instance->update([
                    'last_health_at' => now(),
                    'last_error' => null,
                    'last_version' => $payload['version'] ?? $instance->last_version,
                ]);

                $this->info("اتصال {$instance->getKey()} سالم است.");
            } catch (\Throwable $exception) {
                $instance->update(['last_error' => $exception->getMessage()]);
                $events->dispatchHealthDegraded($instance, $exception->getMessage());
                $this->error("اتصال {$instance->getKey()} ناموفق بود: {$exception->getMessage()}");
            } finally {
                TenantContext::setTenant($previousTenant);
            }
        }

        return self::SUCCESS;
    }
}
