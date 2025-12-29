<?php

namespace Haida\FilamentPettyCashIr\Policies;

use Haida\FilamentPettyCashIr\Models\PettyCashReplenishment;
use Haida\FilamentPettyCashIr\Policies\Concerns\HandlesPettyCashPermissions;

class PettyCashReplenishmentPolicy
{
    use HandlesPettyCashPermissions;

    public function viewAny(): bool
    {
        return $this->allow('petty_cash.replenishment.view');
    }

    public function view(PettyCashReplenishment $replenishment): bool
    {
        return $this->allow('petty_cash.replenishment.view', $replenishment);
    }

    public function create(): bool
    {
        return $this->allow('petty_cash.replenishment.manage');
    }

    public function update(PettyCashReplenishment $replenishment): bool
    {
        return $this->allow('petty_cash.replenishment.manage', $replenishment);
    }

    public function delete(PettyCashReplenishment $replenishment): bool
    {
        return $this->allow('petty_cash.replenishment.manage', $replenishment);
    }

    public function restore(PettyCashReplenishment $replenishment): bool
    {
        return $this->allow('petty_cash.replenishment.manage', $replenishment);
    }

    public function forceDelete(PettyCashReplenishment $replenishment): bool
    {
        return $this->allow('petty_cash.replenishment.manage', $replenishment);
    }

    public function approve(PettyCashReplenishment $replenishment): bool
    {
        return $this->allow('petty_cash.replenishment.approve', $replenishment);
    }

    public function post(PettyCashReplenishment $replenishment): bool
    {
        return $this->allow('petty_cash.replenishment.post', $replenishment);
    }

    public function reject(PettyCashReplenishment $replenishment): bool
    {
        return $this->allow('petty_cash.replenishment.reject', $replenishment);
    }
}
