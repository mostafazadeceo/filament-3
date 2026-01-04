<?php

namespace Haida\FilamentPettyCashIr\Application\UseCases\Settlement;

use Haida\FilamentPettyCashIr\Domain\Rules\SettlementRules;
use Haida\FilamentPettyCashIr\Events\PettyCashEvent;
use Haida\FilamentPettyCashIr\Infrastructure\Audit\AuditLoggerInterface;
use Haida\FilamentPettyCashIr\Models\PettyCashSettlement;
use Haida\FilamentPettyCashIr\Support\PettyCashStatuses;

class ApproveSettlement
{
    public function __construct(
        private readonly SettlementRules $rules,
        private readonly AuditLoggerInterface $auditLogger
    ) {}

    public function handle(PettyCashSettlement $settlement, ?int $actorId = null): PettyCashSettlement
    {
        $this->rules->assertCanApprove($settlement);

        $settlement->update([
            'status' => PettyCashStatuses::SETTLEMENT_APPROVED,
            'approved_by' => $actorId,
            'approved_at' => now(),
        ]);

        $this->auditLogger->log($settlement, $actorId, 'settlement_approved', 'تأیید تسویه');
        event(new PettyCashEvent('settlement.approved', $settlement));

        return $settlement->refresh();
    }
}
