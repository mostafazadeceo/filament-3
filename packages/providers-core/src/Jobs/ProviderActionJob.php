<?php

declare(strict_types=1);

namespace Haida\ProvidersCore\Jobs;

use Filamat\IamSuite\Models\Tenant;
use Filamat\IamSuite\Support\TenantContext;
use Haida\ProvidersCore\Contracts\ProviderAdapter;
use Haida\ProvidersCore\DataTransferObjects\ProviderContext;
use Haida\ProvidersCore\DataTransferObjects\ProviderResult;
use Haida\ProvidersCore\Models\ProviderJobLog;
use Haida\ProvidersCore\Services\ProviderRegistry;
use Haida\ProvidersCore\Support\ProviderAction;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Throwable;

class ProviderActionJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public int $tries;

    public int $timeout;

    public function __construct(
        public ?int $tenantId,
        public string $providerKey,
        public ProviderAction $action,
        public array $payload,
        public int $logId,
        public ?int $connectionId = null,
        public bool $sandbox = false,
    ) {
        $this->tries = (int) config('providers-core.retry.tries', 3);
        $this->timeout = (int) config('providers-core.job_timeout_seconds', 600);
    }

    /**
     * @return array<int, int>
     */
    public function backoff(): array
    {
        return (array) config('providers-core.retry.backoff_seconds', [10, 30, 60]);
    }

    public function handle(ProviderRegistry $registry): void
    {
        $previousTenant = TenantContext::getTenant();
        $previousBypass = TenantContext::shouldBypass();

        if ($this->tenantId) {
            $tenant = Tenant::query()->find($this->tenantId);
            if ($tenant) {
                TenantContext::setTenant($tenant);
            }
            TenantContext::bypass(false);
        } else {
            TenantContext::bypass(true);
        }

        try {
            $log = ProviderJobLog::query()->find($this->logId);
            if (! $log) {
                return;
            }

            $log->fill([
                'status' => 'running',
                'started_at' => $log->started_at ?? now(),
                'attempts' => $log->attempts + 1,
            ])->save();

            $context = new ProviderContext($this->tenantId, $this->connectionId, $this->sandbox);
            $adapter = $registry->resolve($this->providerKey);

            $result = $this->execute($adapter, $context);

            $log->fill([
                'status' => $result->success ? 'succeeded' : 'failed',
                'result' => config('providers-core.logging.store_results', true)
                    ? ['success' => $result->success, 'message' => $result->message, 'data' => $result->data]
                    : null,
                'error_message' => $result->success ? null : $result->message,
                'finished_at' => now(),
            ])->save();
        } catch (Throwable $exception) {
            $log = ProviderJobLog::query()->find($this->logId);
            if ($log) {
                $log->fill([
                    'status' => 'failed',
                    'error_message' => $exception->getMessage(),
                    'finished_at' => now(),
                ])->save();
            }

            throw $exception;
        } finally {
            TenantContext::setTenant($previousTenant);
            TenantContext::bypass($previousBypass);
        }
    }

    protected function execute(ProviderAdapter $adapter, ProviderContext $context): ProviderResult
    {
        return match ($this->action) {
            ProviderAction::SyncProducts => $adapter->syncProducts($context, $this->payload),
            ProviderAction::SyncInventory => $adapter->syncInventory($context, $this->payload),
            ProviderAction::CreateOrder => $adapter->createOrder($context, $this->payload),
            ProviderAction::FulfillOrder => $adapter->fulfillOrder($context, $this->payload),
            ProviderAction::FetchOrderStatus => $adapter->fetchOrderStatus($context, $this->payload),
            ProviderAction::HandleWebhook => $adapter->handleWebhook($context, $this->payload),
        };
    }
}
