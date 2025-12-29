<?php

namespace Haida\FilamentPettyCashIr\Policies;

use Haida\FilamentPettyCashIr\Models\PettyCashFund;
use Haida\FilamentPettyCashIr\Policies\Concerns\HandlesPettyCashPermissions;

class PettyCashFundPolicy
{
    use HandlesPettyCashPermissions;

    public function viewAny(): bool
    {
        return $this->allow('petty_cash.fund.view');
    }

    public function view(PettyCashFund $fund): bool
    {
        return $this->allow('petty_cash.fund.view', $fund);
    }

    public function create(): bool
    {
        return $this->allow('petty_cash.fund.manage');
    }

    public function update(PettyCashFund $fund): bool
    {
        return $this->allow('petty_cash.fund.manage', $fund);
    }

    public function delete(PettyCashFund $fund): bool
    {
        return $this->allow('petty_cash.fund.manage', $fund);
    }

    public function restore(PettyCashFund $fund): bool
    {
        return $this->allow('petty_cash.fund.manage', $fund);
    }

    public function forceDelete(PettyCashFund $fund): bool
    {
        return $this->allow('petty_cash.fund.manage', $fund);
    }
}
