<?php

namespace Haida\FilamentPettyCashIr\Application\UseCases\Replenishment;

use Haida\FilamentPettyCashIr\Domain\Rules\ReplenishmentRules;
use Haida\FilamentPettyCashIr\Events\PettyCashEvent;
use Haida\FilamentPettyCashIr\Infrastructure\Accounting\AccountingAdapterInterface;
use Haida\FilamentPettyCashIr\Infrastructure\Audit\AuditLoggerInterface;
use Haida\FilamentPettyCashIr\Infrastructure\Idempotency\IdempotencyService;
use Haida\FilamentPettyCashIr\Models\PettyCashFund;
use Haida\FilamentPettyCashIr\Models\PettyCashReplenishment;
use Haida\FilamentPettyCashIr\Support\PettyCashStatuses;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ReverseReplenishment
{
    public function __construct(
        private readonly ReplenishmentRules $rules,
        private readonly AccountingAdapterInterface $accounting,
        private readonly AuditLoggerInterface $auditLogger,
        private readonly IdempotencyService $idempotency
    ) {}

    public function handle(
        PettyCashReplenishment $replenishment,
        ?int $actorId = null,
        ?string $idempotencyKey = null,
        ?string $reason = null
    ): PettyCashReplenishment {
        $replenishment->loadMissing('fund');
        $this->rules->assertCanReverse($replenishment);

        $fund = $replenishment->fund;
        if (! $fund) {
            throw ValidationException::withMessages([
                'fund_id' => 'تنخواه معتبر نیست.',
            ]);
        }

        return $this->idempotency->run('replenishment.reverse', $replenishment, $idempotencyKey, $actorId, function () use ($replenishment, $fund, $actorId, $reason): PettyCashReplenishment {
            return DB::transaction(function () use ($replenishment, $fund, $actorId, $reason): PettyCashReplenishment {
                $fund = PettyCashFund::query()->whereKey($fund->getKey())->lockForUpdate()->first();
                if (! $fund) {
                    throw ValidationException::withMessages([
                        'fund_id' => 'تنخواه معتبر نیست.',
                    ]);
                }

                $this->rules->assertFundBalanceForReversal($fund, (float) $replenishment->amount);

                $postingResult = $this->accounting->reverseReplenishment($replenishment);

                $fund->update([
                    'current_balance' => (float) $fund->current_balance - (float) $replenishment->amount,
                ]);

                $replenishment->update([
                    'status' => PettyCashStatuses::REPLENISHMENT_REVERSED,
                    'reversed_by' => $actorId,
                    'reversed_at' => now(),
                    'reversal_journal_entry_id' => $postingResult->journalEntry?->id,
                    'reversal_reason' => $reason,
                ]);

                $this->auditLogger->log($replenishment, $actorId, 'replenishment_reversed', 'برگشت تغذیه تنخواه', [
                    'journal_entry_id' => $postingResult->journalEntry?->id,
                ]);
                event(new PettyCashEvent('replenishment.reversed', $replenishment));

                return $replenishment->refresh();
            });
        }, [
            'reason' => $reason,
        ]);
    }
}
