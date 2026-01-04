<?php

namespace Haida\FilamentPettyCashIr\Policies;

use Haida\FilamentPettyCashIr\Models\PettyCashWorkflowRule;
use Haida\FilamentPettyCashIr\Policies\Concerns\HandlesPettyCashPermissions;

class PettyCashWorkflowRulePolicy
{
    use HandlesPettyCashPermissions;

    public function viewAny(): bool
    {
        return $this->allow('petty_cash.workflow.view');
    }

    public function view(PettyCashWorkflowRule $rule): bool
    {
        return $this->allow('petty_cash.workflow.view', $rule);
    }

    public function create(): bool
    {
        return $this->allow('petty_cash.workflow.manage');
    }

    public function update(PettyCashWorkflowRule $rule): bool
    {
        return $this->allow('petty_cash.workflow.manage', $rule);
    }

    public function delete(PettyCashWorkflowRule $rule): bool
    {
        return $this->allow('petty_cash.workflow.manage', $rule);
    }
}
