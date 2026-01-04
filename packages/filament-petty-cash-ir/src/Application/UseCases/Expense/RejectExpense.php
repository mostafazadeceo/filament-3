<?php

namespace Haida\FilamentPettyCashIr\Application\UseCases\Expense;

use Haida\FilamentPettyCashIr\Domain\Rules\ExpenseRules;
use Haida\FilamentPettyCashIr\Events\PettyCashEvent;
use Haida\FilamentPettyCashIr\Infrastructure\Audit\AuditLoggerInterface;
use Haida\FilamentPettyCashIr\Models\PettyCashExpense;
use Haida\FilamentPettyCashIr\Support\PettyCashStatuses;

class RejectExpense
{
    public function __construct(
        private readonly ExpenseRules $rules,
        private readonly AuditLoggerInterface $auditLogger
    ) {}

    public function handle(PettyCashExpense $expense, ?int $actorId = null): PettyCashExpense
    {
        $this->rules->assertCanReject($expense);

        $expense->update([
            'status' => PettyCashStatuses::EXPENSE_REJECTED,
        ]);

        $this->auditLogger->log($expense, $actorId, 'expense_rejected', 'رد هزینه');
        event(new PettyCashEvent('expense.rejected', $expense));

        return $expense->refresh();
    }
}
