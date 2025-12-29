<?php

namespace Vendor\FilamentAccountingIr\Policies;

use Vendor\FilamentAccountingIr\Models\Cheque;
use Vendor\FilamentAccountingIr\Policies\Concerns\HandlesAccountingPermissions;

class ChequePolicy
{
    use HandlesAccountingPermissions;

    public function viewAny(): bool
    {
        return $this->allow('accounting.treasury.view');
    }

    public function view(Cheque $record): bool
    {
        return $this->allow('accounting.treasury.view', $record);
    }

    public function create(): bool
    {
        return $this->allow('accounting.treasury.manage');
    }

    public function update(Cheque $record): bool
    {
        return $this->allow('accounting.treasury.manage', $record);
    }

    public function delete(Cheque $record): bool
    {
        return $this->allow('accounting.treasury.manage', $record);
    }

    public function restore(Cheque $record): bool
    {
        return $this->allow('accounting.treasury.manage', $record);
    }

    public function forceDelete(Cheque $record): bool
    {
        return $this->allow('accounting.treasury.manage', $record);
    }
}
