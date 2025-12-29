<?php

namespace Vendor\FilamentAccountingIr\Policies;

use Vendor\FilamentAccountingIr\Models\AccountPlan;
use Vendor\FilamentAccountingIr\Policies\Concerns\HandlesAccountingPermissions;

class AccountPlanPolicy
{
    use HandlesAccountingPermissions;

    public function viewAny(): bool
    {
        return $this->allow('accounting.account_plan.view');
    }

    public function view(AccountPlan $plan): bool
    {
        return $this->allow('accounting.account_plan.view', $plan);
    }

    public function create(): bool
    {
        return $this->allow('accounting.account_plan.manage');
    }

    public function update(AccountPlan $plan): bool
    {
        return $this->allow('accounting.account_plan.manage', $plan);
    }

    public function delete(AccountPlan $plan): bool
    {
        return $this->allow('accounting.account_plan.manage', $plan);
    }
}
