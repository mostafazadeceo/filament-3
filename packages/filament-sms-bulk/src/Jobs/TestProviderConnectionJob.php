<?php

declare(strict_types=1);

namespace Haida\SmsBulk\Jobs;

use Filamat\IamSuite\Models\Tenant;
use Filamat\IamSuite\Support\TenantContext;
use Haida\SmsBulk\Services\ProviderClientFactory;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Haida\SmsBulk\Models\SmsBulkProviderConnection;

class TestProviderConnectionJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(
        public readonly int $tenantId,
        public readonly int $providerConnectionId,
    ) {}

    public function handle(ProviderClientFactory $clients): void
    {
        TenantContext::setTenant(Tenant::query()->find($this->tenantId));

        $connection = SmsBulkProviderConnection::query()
            ->where('tenant_id', $this->tenantId)
            ->findOrFail($this->providerConnectionId);

        try {
            $response = $clients->make($connection)->myCredit();
            $credit = (float) (($response['data']['credit'] ?? null) ?: 0);

            $connection->update([
                'status' => 'active',
                'last_tested_at' => now(),
                'last_credit_snapshot' => $credit,
            ]);
        } catch (\Throwable $exception) {
            $connection->update([
                'status' => 'failing',
                'last_tested_at' => now(),
                'meta' => array_merge((array) $connection->meta, ['last_error' => $exception->getMessage()]),
            ]);

            throw $exception;
        }
    }
}
