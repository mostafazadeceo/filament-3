<?php

namespace Vendor\FilamentAccountingIr\Policies;

use Vendor\FilamentAccountingIr\Models\TreasuryTransaction;
use Vendor\FilamentAccountingIr\Policies\Concerns\HandlesAccountingPermissions;

class TreasuryTransactionPolicy
{
    use HandlesAccountingPermissions;

    public function viewAny(): bool
    {
        return $this->allow('accounting.treasury.view');
    }

    public function view(TreasuryTransaction $record): bool
    {
        return $this->allow('accounting.treasury.view', $record);
    }

    public function create(): bool
    {
        return $this->allow('accounting.treasury.manage');
    }

    public function update(TreasuryTransaction $record): bool
    {
        return $this->allow('accounting.treasury.manage', $record);
    }

    public function delete(TreasuryTransaction $record): bool
    {
        return $this->allow('accounting.treasury.manage', $record);
    }

    public function restore(TreasuryTransaction $record): bool
    {
        return $this->allow('accounting.treasury.manage', $record);
    }

    public function forceDelete(TreasuryTransaction $record): bool
    {
        return $this->allow('accounting.treasury.manage', $record);
    }
}
