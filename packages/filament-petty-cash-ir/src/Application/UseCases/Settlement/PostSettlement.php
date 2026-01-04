<?php

namespace Haida\FilamentPettyCashIr\Application\UseCases\Settlement;

use Haida\FilamentPettyCashIr\Domain\Rules\SettlementRules;
use Haida\FilamentPettyCashIr\Events\PettyCashEvent;
use Haida\FilamentPettyCashIr\Infrastructure\Audit\AuditLoggerInterface;
use Haida\FilamentPettyCashIr\Infrastructure\Idempotency\IdempotencyService;
use Haida\FilamentPettyCashIr\Models\PettyCashSettlement;
use Haida\FilamentPettyCashIr\Support\PettyCashStatuses;
use Illuminate\Support\Facades\DB;

class PostSettlement
{
    public function __construct(
        private readonly SettlementRules $rules,
        private readonly AuditLoggerInterface $auditLogger,
        private readonly IdempotencyService $idempotency
    ) {}

    public function handle(PettyCashSettlement $settlement, ?int $actorId = null, ?string $idempotencyKey = null): PettyCashSettlement
    {
        $this->rules->assertCanPost($settlement);
        $settlement->loadMissing('items.expense');

        return $this->idempotency->run('settlement.post', $settlement, $idempotencyKey, $actorId, function () use ($settlement, $actorId): PettyCashSettlement {
            return DB::transaction(function () use ($settlement, $actorId): PettyCashSettlement {
                $total = 0.0;
                foreach ($settlement->items as $item) {
                    if ($item->expense && $item->expense->status === PettyCashStatuses::EXPENSE_PAID) {
                        $item->expense->update([
                            'status' => PettyCashStatuses::EXPENSE_SETTLED,
                        ]);
                        $total += (float) $item->expense->amount;
                    }
                }

                $settlement->update([
                    'status' => PettyCashStatuses::SETTLEMENT_POSTED,
                    'posted_by' => $actorId,
                    'posted_at' => now(),
                    'total_expenses' => $total,
                ]);

                $this->auditLogger->log($settlement, $actorId, 'settlement_posted', 'قطعی‌سازی تسویه');
                event(new PettyCashEvent('settlement.posted', $settlement));

                return $settlement->refresh();
            });
        });
    }
}
