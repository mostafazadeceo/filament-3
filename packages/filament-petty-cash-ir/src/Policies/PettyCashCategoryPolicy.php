<?php

namespace Haida\FilamentPettyCashIr\Policies;

use Haida\FilamentPettyCashIr\Models\PettyCashCategory;
use Haida\FilamentPettyCashIr\Policies\Concerns\HandlesPettyCashPermissions;

class PettyCashCategoryPolicy
{
    use HandlesPettyCashPermissions;

    public function viewAny(): bool
    {
        return $this->allow('petty_cash.category.view');
    }

    public function view(PettyCashCategory $category): bool
    {
        return $this->allow('petty_cash.category.view', $category);
    }

    public function create(): bool
    {
        return $this->allow('petty_cash.category.manage');
    }

    public function update(PettyCashCategory $category): bool
    {
        return $this->allow('petty_cash.category.manage', $category);
    }

    public function delete(PettyCashCategory $category): bool
    {
        return $this->allow('petty_cash.category.manage', $category);
    }

    public function restore(PettyCashCategory $category): bool
    {
        return $this->allow('petty_cash.category.manage', $category);
    }

    public function forceDelete(PettyCashCategory $category): bool
    {
        return $this->allow('petty_cash.category.manage', $category);
    }
}
