<?php

namespace Haida\FilamentPettyCashIr\Policies;

use Haida\FilamentPettyCashIr\Models\PettyCashExpense;
use Haida\FilamentPettyCashIr\Policies\Concerns\HandlesPettyCashPermissions;

class PettyCashExpensePolicy
{
    use HandlesPettyCashPermissions;

    public function viewAny(): bool
    {
        return $this->allow('petty_cash.expense.view');
    }

    public function view(PettyCashExpense $expense): bool
    {
        return $this->allow('petty_cash.expense.view', $expense);
    }

    public function create(): bool
    {
        return $this->allow('petty_cash.expense.manage');
    }

    public function update(PettyCashExpense $expense): bool
    {
        return $this->allow('petty_cash.expense.manage', $expense);
    }

    public function delete(PettyCashExpense $expense): bool
    {
        return $this->allow('petty_cash.expense.manage', $expense);
    }

    public function restore(PettyCashExpense $expense): bool
    {
        return $this->allow('petty_cash.expense.manage', $expense);
    }

    public function forceDelete(PettyCashExpense $expense): bool
    {
        return $this->allow('petty_cash.expense.manage', $expense);
    }

    public function approve(PettyCashExpense $expense): bool
    {
        return $this->allow('petty_cash.expense.approve', $expense);
    }

    public function post(PettyCashExpense $expense): bool
    {
        return $this->allow('petty_cash.expense.post', $expense);
    }

    public function settle(PettyCashExpense $expense): bool
    {
        return $this->allow('petty_cash.expense.settle', $expense);
    }

    public function reject(PettyCashExpense $expense): bool
    {
        return $this->allow('petty_cash.expense.reject', $expense);
    }
}
