<?php

namespace Haida\FilamentPettyCashIr\Policies;

use Haida\FilamentPettyCashIr\Models\PettyCashControlException;
use Haida\FilamentPettyCashIr\Policies\Concerns\HandlesPettyCashPermissions;

class PettyCashControlExceptionPolicy
{
    use HandlesPettyCashPermissions;

    public function viewAny(): bool
    {
        return $this->allow('petty_cash.exceptions.view');
    }

    public function view(PettyCashControlException $exception): bool
    {
        return $this->allow('petty_cash.exceptions.view', $exception);
    }

    public function create(): bool
    {
        return $this->allow('petty_cash.exceptions.manage');
    }

    public function update(PettyCashControlException $exception): bool
    {
        return $this->allow('petty_cash.exceptions.manage', $exception);
    }

    public function delete(PettyCashControlException $exception): bool
    {
        return $this->allow('petty_cash.exceptions.manage', $exception);
    }
}
