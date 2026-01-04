<?php

namespace Haida\FilamentThreeCx\Console\Commands;

use Filamat\IamSuite\Support\TenantContext;
use Haida\FilamentThreeCx\Clients\XapiClient;
use Haida\FilamentThreeCx\Models\ThreeCxInstance;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class ThreeCxOpenApiCacheCommand extends Command
{
    protected $signature = 'threecx:openapi-cache {instance?}';

    protected $description = 'Fetch and cache 3CX OpenAPI spec if available.';

    public function handle(): int
    {
        if (! (bool) config('filament-threecx.openapi_cache.enabled', false)) {
            $this->warn('کش OpenAPI غیرفعال است.');

            return self::SUCCESS;
        }

        $path = (string) config('filament-threecx.openapi_cache.path', '/openapi.json');
        $ttl = (int) config('filament-threecx.openapi_cache.ttl_seconds', 3600);
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
                $spec = $client->request('GET', $path);
                $cacheKey = $this->cacheKey($instance->getKey());

                Cache::put($cacheKey, $spec, $ttl);
                $this->info("OpenAPI برای اتصال {$instance->getKey()} کش شد.");
            } catch (\Throwable $exception) {
                $this->error("کش OpenAPI برای اتصال {$instance->getKey()} ناموفق بود: {$exception->getMessage()}");
            } finally {
                TenantContext::setTenant($previousTenant);
            }
        }

        return self::SUCCESS;
    }

    protected function cacheKey(int $instanceId): string
    {
        return 'threecx:openapi:'.$instanceId;
    }
}
