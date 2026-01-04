<?php

namespace Haida\FilamentPettyCashIr\Policies;

use Haida\FilamentPettyCashIr\Models\PettyCashSettlement;
use Haida\FilamentPettyCashIr\Policies\Concerns\HandlesPettyCashPermissions;

class PettyCashSettlementPolicy
{
    use HandlesPettyCashPermissions;

    public function viewAny(): bool
    {
        return $this->allow('petty_cash.settlement.view');
    }

    public function view(PettyCashSettlement $settlement): bool
    {
        return $this->allow('petty_cash.settlement.view', $settlement);
    }

    public function create(): bool
    {
        return $this->allow('petty_cash.settlement.manage');
    }

    public function update(PettyCashSettlement $settlement): bool
    {
        return $this->allow('petty_cash.settlement.manage', $settlement);
    }

    public function delete(PettyCashSettlement $settlement): bool
    {
        return $this->allow('petty_cash.settlement.manage', $settlement);
    }

    public function restore(PettyCashSettlement $settlement): bool
    {
        return $this->allow('petty_cash.settlement.manage', $settlement);
    }

    public function forceDelete(PettyCashSettlement $settlement): bool
    {
        return $this->allow('petty_cash.settlement.manage', $settlement);
    }

    public function approve(PettyCashSettlement $settlement): bool
    {
        return $this->allow('petty_cash.settlement.approve', $settlement);
    }

    public function post(PettyCashSettlement $settlement): bool
    {
        return $this->allow('petty_cash.settlement.post', $settlement);
    }

    public function reverse(PettyCashSettlement $settlement): bool
    {
        return $this->allow('petty_cash.settlement.reverse', $settlement);
    }
}
