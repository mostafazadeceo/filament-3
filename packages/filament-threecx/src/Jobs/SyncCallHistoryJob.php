<?php

namespace Haida\FilamentThreeCx\Jobs;

use Filamat\IamSuite\Support\TenantContext;
use Haida\FilamentThreeCx\Models\ThreeCxInstance;
use Haida\FilamentThreeCx\Services\ThreeCxSyncService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SyncCallHistoryJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(public int $instanceId) {}

    public function handle(ThreeCxSyncService $syncService): void
    {
        $instance = ThreeCxInstance::query()->find($this->instanceId);
        if (! $instance || ! $instance->xapi_enabled) {
            return;
        }

        $previousTenant = TenantContext::getTenant();

        try {
            TenantContext::setTenant($instance->tenant);
            $syncService->syncCallHistory($instance);
        } finally {
            TenantContext::setTenant($previousTenant);
        }
    }
}
