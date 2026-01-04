<?php

namespace Haida\FilamentPettyCashIr\Application\UseCases\Replenishment;

use Haida\FilamentPettyCashIr\Domain\Rules\ReplenishmentRules;
use Haida\FilamentPettyCashIr\Domain\Workflow\WorkflowEngine;
use Haida\FilamentPettyCashIr\Events\PettyCashEvent;
use Haida\FilamentPettyCashIr\Infrastructure\Accounting\AccountingAdapterInterface;
use Haida\FilamentPettyCashIr\Infrastructure\Audit\AuditLoggerInterface;
use Haida\FilamentPettyCashIr\Infrastructure\Idempotency\IdempotencyService;
use Haida\FilamentPettyCashIr\Models\PettyCashFund;
use Haida\FilamentPettyCashIr\Models\PettyCashReplenishment;
use Haida\FilamentPettyCashIr\Services\PettyCashControlService;
use Haida\FilamentPettyCashIr\Support\PettyCashStatuses;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PostReplenishment
{
    public function __construct(
        private readonly ReplenishmentRules $rules,
        private readonly AccountingAdapterInterface $accounting,
        private readonly WorkflowEngine $workflow,
        private readonly PettyCashControlService $controls,
        private readonly AuditLoggerInterface $auditLogger,
        private readonly IdempotencyService $idempotency
    ) {}

    public function handle(PettyCashReplenishment $replenishment, ?int $actorId = null, ?string $idempotencyKey = null): PettyCashReplenishment
    {
        $replenishment->loadMissing('fund');
        $this->rules->assertCanPost($replenishment);

        $decision = $this->workflow->resolveForReplenishment($replenishment);
        if ($decision->requireSeparation && $actorId && (int) $replenishment->approved_by === $actorId) {
            throw ValidationException::withMessages([
                'actor' => 'پرداخت‌کننده نمی‌تواند تأییدکننده باشد.',
            ]);
        }

        $fund = $replenishment->fund;
        if (! $fund) {
            throw ValidationException::withMessages([
                'fund_id' => 'تنخواه معتبر نیست.',
            ]);
        }

        return $this->idempotency->run('replenishment.post', $replenishment, $idempotencyKey, $actorId, function () use ($replenishment, $fund, $actorId): PettyCashReplenishment {
            return DB::transaction(function () use ($replenishment, $fund, $actorId): PettyCashReplenishment {
                $fund = PettyCashFund::query()->whereKey($fund->getKey())->lockForUpdate()->first();
                if (! $fund) {
                    throw ValidationException::withMessages([
                        'fund_id' => 'تنخواه معتبر نیست.',
                    ]);
                }

                $postingResult = $this->accounting->postReplenishment($replenishment, $fund);

                $fund->update([
                    'current_balance' => (float) $fund->current_balance + (float) $replenishment->amount,
                ]);

                $replenishment->update([
                    'status' => PettyCashStatuses::REPLENISHMENT_PAID,
                    'paid_by' => $actorId,
                    'paid_at' => now(),
                    'accounting_journal_entry_id' => $postingResult->journalEntry?->id,
                    'accounting_treasury_transaction_id' => $postingResult->treasuryTransactionId,
                ]);

                $this->controls->checkFundThreshold($fund);

                $this->auditLogger->log($replenishment, $actorId, 'replenishment_paid', 'پرداخت تغذیه تنخواه', [
                    'journal_entry_id' => $postingResult->journalEntry?->id,
                    'treasury_transaction_id' => $postingResult->treasuryTransactionId,
                ]);
                event(new PettyCashEvent('replenishment.paid', $replenishment));

                return $replenishment->refresh();
            });
        });
    }
}
