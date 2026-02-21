<?php

declare(strict_types=1);

namespace Haida\SmsBulk\Services\Campaign;

use Haida\SmsBulk\Models\SmsBulkCampaign;
use Haida\SmsBulk\Services\AuditLogService;

class CampaignApprovalService
{
    public function __construct(protected AuditLogService $auditLog) {}

    public function markPending(SmsBulkCampaign $campaign): SmsBulkCampaign
    {
        $campaign->update(['approval_state' => 'pending']);

        $this->auditLog->log(
            tenantId: $campaign->tenant_id,
            action: 'campaign.approval.pending',
            subjectType: SmsBulkCampaign::class,
            subjectId: (int) $campaign->getKey(),
            meta: ['campaign_id' => $campaign->getKey()],
        );

        return $campaign->refresh();
    }

    public function approve(SmsBulkCampaign $campaign, int $userId): SmsBulkCampaign
    {
        $campaign->update([
            'approval_state' => 'approved',
            'approved_by' => $userId,
            'approved_at' => now(),
        ]);

        $this->auditLog->log(
            tenantId: $campaign->tenant_id,
            action: 'campaign.approval.approved',
            actorId: $userId,
            actorType: 'user',
            subjectType: SmsBulkCampaign::class,
            subjectId: (int) $campaign->getKey(),
        );

        return $campaign->refresh();
    }

    public function reject(SmsBulkCampaign $campaign, int $userId, ?string $reason = null): SmsBulkCampaign
    {
        $campaign->update([
            'approval_state' => 'rejected',
            'status' => 'failed',
            'meta' => array_merge((array) $campaign->meta, ['rejection_reason' => $reason]),
        ]);

        $this->auditLog->log(
            tenantId: $campaign->tenant_id,
            action: 'campaign.approval.rejected',
            actorId: $userId,
            actorType: 'user',
            subjectType: SmsBulkCampaign::class,
            subjectId: (int) $campaign->getKey(),
            meta: ['reason' => $reason],
        );

        return $campaign->refresh();
    }
}
