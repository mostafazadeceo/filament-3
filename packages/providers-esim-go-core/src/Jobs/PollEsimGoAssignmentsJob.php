<?php

declare(strict_types=1);

namespace Haida\ProvidersEsimGoCore\Jobs;

use Filamat\IamSuite\Models\Tenant;
use Filamat\IamSuite\Support\TenantContext;
use Haida\ProvidersEsimGoCore\Models\EsimGoOrder;
use Haida\ProvidersEsimGoCore\Services\EsimGoOrderService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class PollEsimGoAssignmentsJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public int $tries;

    public function __construct(public int $orderId)
    {
        $this->tries = (int) config('providers-esim-go-core.fulfillment.max_attempts', 6);
    }

    /**
     * @return array<int, int>
     */
    public function backoff(): array
    {
        $seconds = (int) config('providers-esim-go-core.fulfillment.poll_seconds', 120);

        return array_fill(0, max(1, $this->tries), $seconds);
    }

    public function handle(EsimGoOrderService $service): void
    {
        $order = EsimGoOrder::query()->find($this->orderId);
        if (! $order) {
            return;
        }

        $previousTenant = TenantContext::getTenant();
        $previousBypass = TenantContext::shouldBypass();

        if ($order->tenant_id) {
            $tenant = Tenant::query()->find($order->tenant_id);
            if ($tenant) {
                TenantContext::setTenant($tenant);
            }
            TenantContext::bypass(false);
        } else {
            TenantContext::bypass(true);
        }

        try {
            $order = $service->refreshAssignments($order);

            if ($order->status === 'ready') {
                return;
            }

            if ($this->attempts() < $this->tries) {
                $delay = (int) config('providers-esim-go-core.fulfillment.poll_seconds', 120);
                $this->release($delay);
            }
        } finally {
            TenantContext::setTenant($previousTenant);
            TenantContext::bypass($previousBypass);
        }
    }
}
