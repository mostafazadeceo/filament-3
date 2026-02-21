<?php

declare(strict_types=1);

namespace Haida\SmsBulk\Http\Controllers\Api\V1;

use Haida\SmsBulk\Models\SmsBulkCampaignRecipient;
use Haida\SmsBulk\Models\SmsBulkProviderConnection;
use Haida\SmsBulk\Services\ProviderClientFactory;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends ApiController
{
    public function outbox(ProviderClientFactory $factory)
    {
        $connection = SmsBulkProviderConnection::query()->where('tenant_id', $this->tenantId())->firstOrFail();

        return $this->ok($factory->make($connection)->reportOutbox());
    }

    public function inbox(ProviderClientFactory $factory)
    {
        $connection = SmsBulkProviderConnection::query()->where('tenant_id', $this->tenantId())->firstOrFail();

        return $this->ok($factory->make($connection)->reportInbox());
    }

    public function bulkRecipients(int $campaignId)
    {
        $items = SmsBulkCampaignRecipient::query()
            ->where('tenant_id', $this->tenantId())
            ->where('campaign_id', $campaignId)
            ->get()
            ->toArray();

        return $this->ok(['items' => $items]);
    }

    public function exportCsv(Request $request): StreamedResponse
    {
        $campaignId = (int) $request->query('campaign_id');

        $items = SmsBulkCampaignRecipient::query()
            ->where('tenant_id', $this->tenantId())
            ->when($campaignId > 0, fn ($query) => $query->where('campaign_id', $campaignId))
            ->orderBy('id')
            ->get(['campaign_id', 'msisdn', 'status', 'cost', 'error_code', 'error_message', 'delivered_at']);

        return response()->streamDownload(function () use ($items): void {
            $handle = fopen('php://output', 'wb');
            fputcsv($handle, ['campaign_id', 'msisdn', 'status', 'cost', 'error_code', 'error_message', 'delivered_at']);
            foreach ($items as $row) {
                fputcsv($handle, [
                    $row->campaign_id,
                    $row->msisdn,
                    $row->status,
                    $row->cost,
                    $row->error_code,
                    $row->error_message,
                    $row->delivered_at,
                ]);
            }
            fclose($handle);
        }, 'sms-bulk-report.csv');
    }
}
