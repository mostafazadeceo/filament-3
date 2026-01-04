<?php

namespace Haida\FilamentPettyCashIr\Policies;

use Haida\FilamentPettyCashIr\Models\PettyCashReconciliation;
use Haida\FilamentPettyCashIr\Policies\Concerns\HandlesPettyCashPermissions;

class PettyCashReconciliationPolicy
{
    use HandlesPettyCashPermissions;

    public function viewAny(): bool
    {
        return $this->allow('petty_cash.controls.reconcile.view');
    }

    public function view(PettyCashReconciliation $reconciliation): bool
    {
        return $this->allow('petty_cash.controls.reconcile.view', $reconciliation);
    }

    public function create(): bool
    {
        return $this->allow('petty_cash.controls.reconcile.manage');
    }

    public function update(PettyCashReconciliation $reconciliation): bool
    {
        return $this->allow('petty_cash.controls.reconcile.manage', $reconciliation);
    }

    public function delete(PettyCashReconciliation $reconciliation): bool
    {
        return $this->allow('petty_cash.controls.reconcile.manage', $reconciliation);
    }
}
