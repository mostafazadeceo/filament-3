<?php

declare(strict_types=1);

namespace Haida\SmsBulk\Services\Campaign;

use Haida\SmsBulk\Exceptions\ApprovalRequiredException;
use Haida\SmsBulk\Exceptions\QuotaExceededException;
use Haida\SmsBulk\Models\SmsBulkCampaign;
use Haida\SmsBulk\Models\SmsBulkProviderConnection;
use Haida\SmsBulk\Services\AuditLogService;
use Haida\SmsBulk\Services\MessageValidationService;
use Haida\SmsBulk\Services\QuotaService;
use Illuminate\Support\Arr;

class CampaignBuilderService
{
    public function __construct(
        protected CampaignPricingService $pricing,
        protected QuotaService $quota,
        protected MessageValidationService $validator,
        protected AuditLogService $auditLog,
    ) {}

    /**
     * @param array<string, mixed> $payload
     * @throws ApprovalRequiredException
     * @throws QuotaExceededException
     */
    public function createDraft(
        SmsBulkProviderConnection $connection,
        array $payload,
        int $recipientCount,
        ?int $actorId = null,
    ): SmsBulkCampaign {
        $this->validator->validate($payload);

        $pricing = $this->pricing->estimate($connection, $payload, $recipientCount);
        $estimate = (float) $pricing['estimate'];

        $this->quota->assertCanEnqueue($connection->tenant_id, $recipientCount, $estimate);

        $approvalState = 'approved';
        if ($this->quota->requiresApproval($connection->tenant_id, $estimate)) {
            $approvalState = 'pending';
        }

        $campaign = SmsBulkCampaign::create([
            'tenant_id' => $connection->tenant_id,
            'provider_connection_id' => $connection->getKey(),
            'name' => (string) ($payload['name'] ?? 'SMS Campaign'),
            'mode' => (string) ($payload['mode'] ?? 'standard'),
            'language' => (string) ($payload['language'] ?? 'fa'),
            'encoding' => (string) ($payload['encoding'] ?? 'auto'),
            'sender' => (string) ($payload['sender'] ?? $connection->default_sender ?? ''),
            'cost_center' => Arr::get($payload, 'cost_center'),
            'schedule_at' => Arr::get($payload, 'schedule_at'),
            'quiet_hours_profile_id' => Arr::get($payload, 'quiet_hours_profile_id'),
            'approval_state' => $approvalState,
            'cost_estimate' => $estimate,
            'pricing_snapshot' => $pricing['pricing_snapshot'],
            'payload_snapshot' => $payload,
            'idempotency_key' => (string) ($payload['idempotency_key'] ?? sha1(json_encode($payload) ?: '')),
            'status' => 'draft',
        ]);

        $this->auditLog->log(
            tenantId: $campaign->tenant_id,
            action: 'campaign.created',
            actorId: $actorId,
            actorType: $actorId ? 'user' : null,
            subjectType: SmsBulkCampaign::class,
            subjectId: (int) $campaign->getKey(),
            meta: ['approval_state' => $approvalState, 'cost_estimate' => $estimate],
        );

        if ($approvalState === 'pending') {
            throw new ApprovalRequiredException('Campaign requires approval before submission.');
        }

        return $campaign;
    }
}
