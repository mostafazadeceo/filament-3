<?php

declare(strict_types=1);

namespace Haida\SmsBulk\Jobs;

use Filamat\IamSuite\Models\Tenant;
use Filamat\IamSuite\Support\TenantContext;
use Haida\SmsBulk\Models\SmsBulkCampaign;
use Haida\SmsBulk\Models\SmsBulkCampaignRecipient;
use Haida\SmsBulk\Services\ProviderClientFactory;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SyncReportsJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(
        public readonly int $tenantId,
        public readonly int $campaignId,
    ) {}

    public function handle(ProviderClientFactory $clients): void
    {
        TenantContext::setTenant(Tenant::query()->find($this->tenantId));

        $campaign = SmsBulkCampaign::query()
            ->with('providerConnection')
            ->where('tenant_id', $this->tenantId)
            ->findOrFail($this->campaignId);

        $bulkId = (string) (($campaign->meta['bulk_id'] ?? '') ?: '');
        if ($bulkId === '') {
            return;
        }

        $response = $clients->make($campaign->providerConnection)->reportBulkRecipients($bulkId);
        $rows = (array) ($response['data']['items'] ?? $response['data'] ?? []);

        foreach ($rows as $row) {
            $data = (array) $row;
            $msisdn = (string) ($data['recipient'] ?? $data['to'] ?? '');
            if ($msisdn === '') {
                continue;
            }

            SmsBulkCampaignRecipient::query()
                ->where('tenant_id', $this->tenantId)
                ->where('campaign_id', $campaign->getKey())
                ->where('msisdn', $msisdn)
                ->update([
                    'status' => (string) ($data['status'] ?? 'sent'),
                    'delivered_at' => isset($data['delivered_at']) ? now()->parse((string) $data['delivered_at']) : null,
                    'error_code' => $data['error_code'] ?? null,
                    'error_message' => $data['error_message'] ?? null,
                ]);
        }
    }
}
