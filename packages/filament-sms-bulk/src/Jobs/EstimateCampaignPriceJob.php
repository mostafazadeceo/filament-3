<?php

declare(strict_types=1);

namespace Haida\SmsBulk\Jobs;

use Filamat\IamSuite\Models\Tenant;
use Filamat\IamSuite\Support\TenantContext;
use Haida\SmsBulk\Models\SmsBulkCampaign;
use Haida\SmsBulk\Services\Campaign\CampaignPricingService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class EstimateCampaignPriceJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(
        public readonly int $tenantId,
        public readonly int $campaignId,
    ) {}

    public function handle(CampaignPricingService $pricing): void
    {
        TenantContext::setTenant(Tenant::query()->find($this->tenantId));

        $campaign = SmsBulkCampaign::query()
            ->with('providerConnection')
            ->where('tenant_id', $this->tenantId)
            ->findOrFail($this->campaignId);

        $recipients = (array) (($campaign->payload_snapshot ?? [])['recipients'] ?? []);

        $result = $pricing->estimate(
            $campaign->providerConnection,
            (array) ($campaign->payload_snapshot ?? []),
            count($recipients),
        );

        $campaign->update([
            'cost_estimate' => $result['estimate'],
            'pricing_snapshot' => $result['pricing_snapshot'],
        ]);
    }
}
