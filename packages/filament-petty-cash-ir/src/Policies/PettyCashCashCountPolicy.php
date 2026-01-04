<?php

namespace Haida\FilamentPettyCashIr\Policies;

use Haida\FilamentPettyCashIr\Models\PettyCashCashCount;
use Haida\FilamentPettyCashIr\Policies\Concerns\HandlesPettyCashPermissions;

class PettyCashCashCountPolicy
{
    use HandlesPettyCashPermissions;

    public function viewAny(): bool
    {
        return $this->allow('petty_cash.controls.cash_count.view');
    }

    public function view(PettyCashCashCount $count): bool
    {
        return $this->allow('petty_cash.controls.cash_count.view', $count);
    }

    public function create(): bool
    {
        return $this->allow('petty_cash.controls.cash_count.manage');
    }

    public function update(PettyCashCashCount $count): bool
    {
        return $this->allow('petty_cash.controls.cash_count.manage', $count);
    }

    public function delete(PettyCashCashCount $count): bool
    {
        return $this->allow('petty_cash.controls.cash_count.manage', $count);
    }
}
