<?php

namespace Haida\FilamentPettyCashIr\Application\UseCases\Expense;

use Haida\FilamentPettyCashIr\Domain\Rules\ExpenseRules;
use Haida\FilamentPettyCashIr\Events\PettyCashEvent;
use Haida\FilamentPettyCashIr\Infrastructure\Accounting\AccountingAdapterInterface;
use Haida\FilamentPettyCashIr\Infrastructure\Audit\AuditLoggerInterface;
use Haida\FilamentPettyCashIr\Infrastructure\Idempotency\IdempotencyService;
use Haida\FilamentPettyCashIr\Models\PettyCashExpense;
use Haida\FilamentPettyCashIr\Models\PettyCashFund;
use Haida\FilamentPettyCashIr\Support\PettyCashStatuses;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ReverseExpense
{
    public function __construct(
        private readonly ExpenseRules $rules,
        private readonly AccountingAdapterInterface $accounting,
        private readonly AuditLoggerInterface $auditLogger,
        private readonly IdempotencyService $idempotency
    ) {}

    public function handle(
        PettyCashExpense $expense,
        ?int $actorId = null,
        ?string $idempotencyKey = null,
        ?string $reason = null
    ): PettyCashExpense {
        $expense->loadMissing('fund');
        $this->rules->assertCanReverse($expense);

        $fund = $expense->fund;
        if (! $fund) {
            throw ValidationException::withMessages([
                'fund_id' => 'تنخواه معتبر نیست.',
            ]);
        }

        return $this->idempotency->run('expense.reverse', $expense, $idempotencyKey, $actorId, function () use ($expense, $fund, $actorId, $reason): PettyCashExpense {
            return DB::transaction(function () use ($expense, $fund, $actorId, $reason): PettyCashExpense {
                $fund = PettyCashFund::query()->whereKey($fund->getKey())->lockForUpdate()->first();
                if (! $fund) {
                    throw ValidationException::withMessages([
                        'fund_id' => 'تنخواه معتبر نیست.',
                    ]);
                }

                $journalEntry = $this->accounting->reverseExpense($expense)->journalEntry;

                $fund->update([
                    'current_balance' => (float) $fund->current_balance + (float) $expense->amount,
                ]);

                $expense->update([
                    'status' => PettyCashStatuses::EXPENSE_REVERSED,
                    'reversed_by' => $actorId,
                    'reversed_at' => now(),
                    'reversal_journal_entry_id' => $journalEntry?->id,
                    'reversal_reason' => $reason,
                ]);

                $this->auditLogger->log($expense, $actorId, 'expense_reversed', 'برگشت هزینه', [
                    'journal_entry_id' => $journalEntry?->id,
                ]);
                event(new PettyCashEvent('expense.reversed', $expense));

                return $expense->refresh();
            });
        }, [
            'reason' => $reason,
        ]);
    }
}
