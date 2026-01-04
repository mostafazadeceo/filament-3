<?php

namespace Haida\FilamentPettyCashIr\Domain\Workflow;

use Haida\FilamentPettyCashIr\Models\PettyCashExpense;
use Haida\FilamentPettyCashIr\Models\PettyCashReplenishment;
use Haida\FilamentPettyCashIr\Models\PettyCashWorkflowRule;

class WorkflowEngine
{
    public function resolveForExpense(PettyCashExpense $expense): WorkflowDecision
    {
        return $this->resolve(
            'expense',
            (int) $expense->tenant_id,
            (int) $expense->company_id,
            $expense->fund_id,
            $expense->category_id,
            (float) $expense->amount
        );
    }

    public function resolveForReplenishment(PettyCashReplenishment $replenishment): WorkflowDecision
    {
        return $this->resolve(
            'replenishment',
            (int) $replenishment->tenant_id,
            (int) $replenishment->company_id,
            $replenishment->fund_id,
            null,
            (float) $replenishment->amount
        );
    }

    protected function resolve(
        string $transactionType,
        int $tenantId,
        int $companyId,
        ?int $fundId,
        ?int $categoryId,
        float $amount
    ): WorkflowDecision {
        $rules = PettyCashWorkflowRule::query()
            ->where('tenant_id', $tenantId)
            ->where('company_id', $companyId)
            ->where('transaction_type', $transactionType)
            ->where('status', 'active')
            ->where(fn ($query) => $query->whereNull('fund_id')->orWhere('fund_id', $fundId))
            ->where(fn ($query) => $query->whereNull('category_id')->orWhere('category_id', $categoryId))
            ->where(fn ($query) => $query->whereNull('min_amount')->orWhere('min_amount', '<=', $amount))
            ->where(fn ($query) => $query->whereNull('max_amount')->orWhere('max_amount', '>=', $amount))
            ->get();

        if ($rules->isEmpty()) {
            return new WorkflowDecision(null, 1, false, null);
        }

        $rule = $rules->sortByDesc(function (PettyCashWorkflowRule $rule): int {
            return ($rule->fund_id ? 2 : 0) + ($rule->category_id ? 1 : 0);
        })->first();

        if (! $rule) {
            return new WorkflowDecision(null, 1, false, null);
        }

        return new WorkflowDecision(
            $rule->getKey(),
            max(1, (int) $rule->steps_required),
            (bool) $rule->require_separation,
            $rule->require_receipt
        );
    }
}
