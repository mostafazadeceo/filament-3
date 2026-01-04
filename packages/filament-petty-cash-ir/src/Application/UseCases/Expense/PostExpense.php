<?php

namespace Haida\FilamentPettyCashIr\Application\UseCases\Expense;

use Haida\FilamentPettyCashIr\Domain\Rules\ExpenseRules;
use Haida\FilamentPettyCashIr\Domain\Workflow\WorkflowEngine;
use Haida\FilamentPettyCashIr\Events\PettyCashEvent;
use Haida\FilamentPettyCashIr\Infrastructure\Accounting\AccountingAdapterInterface;
use Haida\FilamentPettyCashIr\Infrastructure\Audit\AuditLoggerInterface;
use Haida\FilamentPettyCashIr\Infrastructure\Idempotency\IdempotencyService;
use Haida\FilamentPettyCashIr\Models\PettyCashExpense;
use Haida\FilamentPettyCashIr\Models\PettyCashFund;
use Haida\FilamentPettyCashIr\Services\PettyCashControlService;
use Haida\FilamentPettyCashIr\Support\PettyCashStatuses;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PostExpense
{
    public function __construct(
        private readonly ExpenseRules $rules,
        private readonly AccountingAdapterInterface $accounting,
        private readonly WorkflowEngine $workflow,
        private readonly PettyCashControlService $controls,
        private readonly AuditLoggerInterface $auditLogger,
        private readonly IdempotencyService $idempotency
    ) {}

    public function handle(PettyCashExpense $expense, ?int $actorId = null, ?string $idempotencyKey = null): PettyCashExpense
    {
        $expense->loadMissing('fund', 'category');
        $this->rules->assertCanPost($expense);
        $this->rules->assertReceiptSatisfied($expense);

        $decision = $this->workflow->resolveForExpense($expense);
        if ($decision->requireSeparation && $actorId && (int) $expense->approved_by === $actorId) {
            throw ValidationException::withMessages([
                'actor' => 'پرداخت‌کننده نمی‌تواند تأییدکننده باشد.',
            ]);
        }

        $fund = $expense->fund;
        if (! $fund) {
            throw ValidationException::withMessages([
                'fund_id' => 'تنخواه معتبر نیست.',
            ]);
        }

        return $this->idempotency->run('expense.post', $expense, $idempotencyKey, $actorId, function () use ($expense, $fund, $actorId): PettyCashExpense {
            return DB::transaction(function () use ($expense, $fund, $actorId): PettyCashExpense {
                $fund = PettyCashFund::query()->whereKey($fund->getKey())->lockForUpdate()->first();
                if (! $fund) {
                    throw ValidationException::withMessages([
                        'fund_id' => 'تنخواه معتبر نیست.',
                    ]);
                }

                $this->rules->assertFundBalance($fund, (float) $expense->amount);

                $journalEntry = $this->accounting->postExpense($expense, $fund)->journalEntry;

                $fund->update([
                    'current_balance' => (float) $fund->current_balance - (float) $expense->amount,
                ]);

                $expense->update([
                    'status' => PettyCashStatuses::EXPENSE_PAID,
                    'paid_by' => $actorId,
                    'paid_at' => now(),
                    'has_receipt' => $expense->attachments()->exists(),
                    'accounting_journal_entry_id' => $journalEntry?->id,
                ]);

                $this->controls->checkFundThreshold($fund);

                $this->auditLogger->log($expense, $actorId, 'expense_paid', 'پرداخت هزینه', [
                    'journal_entry_id' => $journalEntry?->id,
                ]);
                event(new PettyCashEvent('expense.paid', $expense));

                return $expense->refresh();
            });
        });
    }
}
