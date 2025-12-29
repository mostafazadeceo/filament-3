<?php

namespace Vendor\FilamentAccountingIr\Policies;

use Vendor\FilamentAccountingIr\Models\TreasuryAccount;
use Vendor\FilamentAccountingIr\Policies\Concerns\HandlesAccountingPermissions;

class TreasuryAccountPolicy
{
    use HandlesAccountingPermissions;

    public function viewAny(): bool
    {
        return $this->allow('accounting.treasury.view');
    }

    public function view(TreasuryAccount $account): bool
    {
        return $this->allow('accounting.treasury.view', $account);
    }

    public function create(): bool
    {
        return $this->allow('accounting.treasury.manage');
    }

    public function update(TreasuryAccount $account): bool
    {
        return $this->allow('accounting.treasury.manage', $account);
    }

    public function delete(TreasuryAccount $account): bool
    {
        return $this->allow('accounting.treasury.manage', $account);
    }
}
