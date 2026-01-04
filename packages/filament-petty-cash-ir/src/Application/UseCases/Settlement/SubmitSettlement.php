<?php

namespace Haida\FilamentPettyCashIr\Application\UseCases\Settlement;

use Haida\FilamentPettyCashIr\Domain\Rules\SettlementRules;
use Haida\FilamentPettyCashIr\Events\PettyCashEvent;
use Haida\FilamentPettyCashIr\Infrastructure\Audit\AuditLoggerInterface;
use Haida\FilamentPettyCashIr\Models\PettyCashSettlement;
use Haida\FilamentPettyCashIr\Support\PettyCashStatuses;

class SubmitSettlement
{
    public function __construct(
        private readonly SettlementRules $rules,
        private readonly AuditLoggerInterface $auditLogger
    ) {}

    public function handle(PettyCashSettlement $settlement, ?int $actorId = null): PettyCashSettlement
    {
        $this->rules->assertCanSubmit($settlement);

        $settlement->update([
            'status' => PettyCashStatuses::SETTLEMENT_SUBMITTED,
            'requested_by' => $settlement->requested_by ?: $actorId,
        ]);

        $this->auditLogger->log($settlement, $actorId, 'settlement_submitted', 'ارسال تسویه');
        event(new PettyCashEvent('settlement.submitted', $settlement));

        return $settlement->refresh();
    }
}
