<?php

declare(strict_types=1);

namespace Haida\SmsBulk\Jobs;

use Filamat\IamSuite\Models\Tenant;
use Filamat\IamSuite\Support\TenantContext;
use Haida\SmsBulk\Models\SmsBulkCampaign;
use Haida\SmsBulk\Models\SmsBulkCampaignRecipient;
use Haida\SmsBulk\Services\AuditLogService;
use Haida\SmsBulk\Services\SuppressionService;
use Haida\SmsBulk\Support\IdempotencyGuard;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class EnqueueCampaignJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(
        public readonly int $tenantId,
        public readonly int $campaignId,
    ) {}

    public function handle(SuppressionService $suppression, AuditLogService $auditLog, IdempotencyGuard $idempotency): void
    {
        TenantContext::setTenant(Tenant::query()->find($this->tenantId));

        $campaign = SmsBulkCampaign::query()
            ->where('tenant_id', $this->tenantId)
            ->findOrFail($this->campaignId);

        if ($campaign->approval_state === 'pending') {
            return;
        }

        $payload = (array) ($campaign->payload_snapshot ?? []);
        $recipients = array_values(array_unique((array) ($payload['recipients'] ?? [])));
        $overrideSuppression = (bool) ($payload['override_suppression'] ?? false);

        $blockedByPolicy = [];
        if ($overrideSuppression) {
            $baseline = $suppression->filterRecipients(
                tenantId: $this->tenantId,
                recipients: $recipients,
                overrideSuppression: false,
            );
            $blockedByPolicy = $baseline['blocked'];
        }

        $filtered = $suppression->filterRecipients(
            tenantId: $this->tenantId,
            recipients: $recipients,
            overrideSuppression: $overrideSuppression,
        );

        $allowed = $filtered['allowed'];
        $blocked = $filtered['blocked'];

        foreach ($allowed as $msisdn) {
            SmsBulkCampaignRecipient::query()->updateOrCreate(
                [
                    'tenant_id' => $this->tenantId,
                    'campaign_id' => $campaign->getKey(),
                    'msisdn' => $msisdn,
                ],
                [
                    'status' => 'queued',
                    'variables' => (array) ($payload['variables'][$msisdn] ?? []),
                ],
            );
        }

        foreach ($blocked as $msisdn) {
            SmsBulkCampaignRecipient::query()->updateOrCreate(
                [
                    'tenant_id' => $this->tenantId,
                    'campaign_id' => $campaign->getKey(),
                    'msisdn' => $msisdn,
                ],
                [
                    'status' => 'suppressed',
                    'error_code' => 'suppressed',
                    'error_message' => 'Recipient is opted-out or suppressed.',
                ],
            );
        }

        $campaign->update(['status' => 'queued']);

        if ($overrideSuppression && $blockedByPolicy !== []) {
            $auditLog->log(
                tenantId: $campaign->tenant_id,
                action: 'campaign.suppression.override',
                subjectType: SmsBulkCampaign::class,
                subjectId: (int) $campaign->getKey(),
                meta: ['blocked_count' => count($blockedByPolicy)],
            );
        }

        $chunkSize = max(1, (int) config('filament-sms-bulk.queue.chunk_size', 500));
        $ids = SmsBulkCampaignRecipient::query()
            ->where('tenant_id', $this->tenantId)
            ->where('campaign_id', $campaign->getKey())
            ->where('status', 'queued')
            ->pluck('id')
            ->all();

        foreach (array_chunk($ids, $chunkSize) as $chunk) {
            $key = sprintf('%d:%d:%s', $this->tenantId, $campaign->getKey(), sha1(json_encode($chunk) ?: ''));

            $idempotency->once($key, 600, function () use ($chunk): void {
                SendCampaignChunkJob::dispatch($this->tenantId, $this->campaignId, $chunk);
            });
        }
    }
}
