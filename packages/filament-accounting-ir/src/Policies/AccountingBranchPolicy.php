<?php

namespace Vendor\FilamentAccountingIr\Policies;

use Vendor\FilamentAccountingIr\Models\AccountingBranch;
use Vendor\FilamentAccountingIr\Policies\Concerns\HandlesAccountingPermissions;

class AccountingBranchPolicy
{
    use HandlesAccountingPermissions;

    public function viewAny(): bool
    {
        return $this->allow('accounting.branch.view');
    }

    public function view(AccountingBranch $branch): bool
    {
        return $this->allow('accounting.branch.view', $branch);
    }

    public function create(): bool
    {
        return $this->allow('accounting.branch.manage');
    }

    public function update(AccountingBranch $branch): bool
    {
        return $this->allow('accounting.branch.manage', $branch);
    }

    public function delete(AccountingBranch $branch): bool
    {
        return $this->allow('accounting.branch.manage', $branch);
    }

    public function restore(AccountingBranch $branch): bool
    {
        return $this->allow('accounting.branch.manage', $branch);
    }

    public function forceDelete(AccountingBranch $branch): bool
    {
        return $this->allow('accounting.branch.manage', $branch);
    }
}
