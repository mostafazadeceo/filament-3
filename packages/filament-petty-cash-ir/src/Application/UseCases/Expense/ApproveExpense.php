<?php

namespace Haida\FilamentPettyCashIr\Application\UseCases\Expense;

use Haida\FilamentPettyCashIr\Domain\Rules\ExpenseRules;
use Haida\FilamentPettyCashIr\Domain\Workflow\WorkflowEngine;
use Haida\FilamentPettyCashIr\Events\PettyCashEvent;
use Haida\FilamentPettyCashIr\Infrastructure\Audit\AuditLoggerInterface;
use Haida\FilamentPettyCashIr\Models\PettyCashExpense;
use Haida\FilamentPettyCashIr\Support\PettyCashStatuses;
use Illuminate\Validation\ValidationException;

class ApproveExpense
{
    public function __construct(
        private readonly ExpenseRules $rules,
        private readonly WorkflowEngine $workflow,
        private readonly AuditLoggerInterface $auditLogger
    ) {}

    public function handle(PettyCashExpense $expense, ?int $actorId = null): PettyCashExpense
    {
        $this->rules->assertCanApprove($expense);

        $decision = $this->workflow->resolveForExpense($expense);

        if ($decision->requireSeparation && $actorId && (int) $expense->requested_by === $actorId) {
            throw ValidationException::withMessages([
                'actor' => 'تأییدکننده نمی‌تواند ثبت‌کننده باشد.',
            ]);
        }

        $stepsRequired = $expense->approval_steps_required ?: $decision->stepsRequired;
        $stepsCompleted = (int) $expense->approval_steps_completed + 1;
        $history = $expense->approval_history ?? [];
        $history[] = [
            'actor_id' => $actorId,
            'approved_at' => now()->toISOString(),
            'step' => $stepsCompleted,
        ];

        $updates = [
            'workflow_rule_id' => $decision->ruleId,
            'approval_steps_required' => $stepsRequired,
            'approval_steps_completed' => $stepsCompleted,
            'approval_history' => $history,
        ];

        if ($stepsCompleted < $stepsRequired) {
            $expense->update($updates);
            $this->auditLogger->log($expense, $actorId, 'expense_approval_step', 'گام تأیید هزینه', [
                'step' => $stepsCompleted,
                'steps_required' => $stepsRequired,
            ]);
            event(new PettyCashEvent('expense.approval_step', $expense));

            return $expense->refresh();
        }

        $expense->update($updates + [
            'status' => PettyCashStatuses::EXPENSE_APPROVED,
            'approved_by' => $actorId,
            'approved_at' => now(),
        ]);

        $this->auditLogger->log($expense, $actorId, 'expense_approved', 'تأیید هزینه', [
            'steps_required' => $stepsRequired,
        ]);
        event(new PettyCashEvent('expense.approved', $expense));

        return $expense->refresh();
    }
}
