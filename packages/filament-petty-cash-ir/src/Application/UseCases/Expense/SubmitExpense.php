<?php

namespace Haida\FilamentPettyCashIr\Application\UseCases\Expense;

use Haida\FilamentPettyCashIr\Domain\Rules\ExpenseRules;
use Haida\FilamentPettyCashIr\Domain\Workflow\WorkflowEngine;
use Haida\FilamentPettyCashIr\Events\PettyCashEvent;
use Haida\FilamentPettyCashIr\Infrastructure\Audit\AuditLoggerInterface;
use Haida\FilamentPettyCashIr\Models\PettyCashExpense;
use Haida\FilamentPettyCashIr\Support\PettyCashStatuses;

class SubmitExpense
{
    public function __construct(
        private readonly ExpenseRules $rules,
        private readonly WorkflowEngine $workflow,
        private readonly AuditLoggerInterface $auditLogger
    ) {}

    public function handle(PettyCashExpense $expense, ?int $actorId = null): PettyCashExpense
    {
        $this->rules->assertCanSubmit($expense);

        $decision = $this->workflow->resolveForExpense($expense);
        $receiptRequired = $decision->requireReceipt;
        if ($receiptRequired === null) {
            $receiptRequired = (bool) config('filament-petty-cash-ir.workflow.require_attachments', true);
        }

        $expense->update([
            'status' => PettyCashStatuses::EXPENSE_SUBMITTED,
            'requested_by' => $expense->requested_by ?: $actorId,
            'workflow_rule_id' => $decision->ruleId,
            'approval_steps_required' => $decision->stepsRequired,
            'approval_steps_completed' => 0,
            'approval_history' => [],
            'receipt_required' => $receiptRequired,
        ]);

        $this->auditLogger->log($expense, $actorId, 'expense_submitted', 'ارسال هزینه');
        event(new PettyCashEvent('expense.submitted', $expense));

        return $expense->refresh();
    }
}
