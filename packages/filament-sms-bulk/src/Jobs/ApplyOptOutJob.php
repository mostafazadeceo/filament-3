<?php

declare(strict_types=1);

namespace Haida\SmsBulk\Jobs;

use Filamat\IamSuite\Models\Tenant;
use Filamat\IamSuite\Support\TenantContext;
use Haida\SmsBulk\Services\SuppressionService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ApplyOptOutJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(
        public readonly int $tenantId,
        public readonly string $msisdn,
        public readonly string $source = 'keyword',
        public readonly ?int $actorId = null,
    ) {}

    public function handle(SuppressionService $suppression): void
    {
        TenantContext::setTenant(Tenant::query()->find($this->tenantId));

        $suppression->applyOptOut(
            tenantId: $this->tenantId,
            msisdn: $this->msisdn,
            source: $this->source,
            actorId: $this->actorId,
        );
    }
}
