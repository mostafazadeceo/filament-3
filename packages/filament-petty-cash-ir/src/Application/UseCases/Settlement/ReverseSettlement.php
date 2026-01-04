<?php

namespace Haida\FilamentPettyCashIr\Application\UseCases\Settlement;

use Haida\FilamentPettyCashIr\Domain\Rules\SettlementRules;
use Haida\FilamentPettyCashIr\Events\PettyCashEvent;
use Haida\FilamentPettyCashIr\Infrastructure\Audit\AuditLoggerInterface;
use Haida\FilamentPettyCashIr\Infrastructure\Idempotency\IdempotencyService;
use Haida\FilamentPettyCashIr\Models\PettyCashSettlement;
use Haida\FilamentPettyCashIr\Support\PettyCashStatuses;
use Illuminate\Support\Facades\DB;

class ReverseSettlement
{
    public function __construct(
        private readonly SettlementRules $rules,
        private readonly AuditLoggerInterface $auditLogger,
        private readonly IdempotencyService $idempotency
    ) {}

    public function handle(
        PettyCashSettlement $settlement,
        ?int $actorId = null,
        ?string $idempotencyKey = null,
        ?string $reason = null
    ): PettyCashSettlement {
        $this->rules->assertCanReverse($settlement);
        $settlement->loadMissing('items.expense');

        return $this->idempotency->run('settlement.reverse', $settlement, $idempotencyKey, $actorId, function () use ($settlement, $actorId, $reason): PettyCashSettlement {
            return DB::transaction(function () use ($settlement, $actorId, $reason): PettyCashSettlement {
                foreach ($settlement->items as $item) {
                    if ($item->expense && $item->expense->status === PettyCashStatuses::EXPENSE_SETTLED) {
                        $item->expense->update([
                            'status' => PettyCashStatuses::EXPENSE_PAID,
                        ]);
                    }
                }

                $settlement->update([
                    'status' => PettyCashStatuses::SETTLEMENT_REVERSED,
                    'reversed_by' => $actorId,
                    'reversed_at' => now(),
                    'reversal_reason' => $reason,
                ]);

                $this->auditLogger->log($settlement, $actorId, 'settlement_reversed', 'برگشت تسویه', [
                    'reason' => $reason,
                ]);
                event(new PettyCashEvent('settlement.reversed', $settlement));

                return $settlement->refresh();
            });
        }, [
            'reason' => $reason,
        ]);
    }
}
