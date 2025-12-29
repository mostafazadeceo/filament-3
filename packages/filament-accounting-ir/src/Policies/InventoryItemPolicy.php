<?php

namespace Vendor\FilamentAccountingIr\Policies;

use Vendor\FilamentAccountingIr\Models\InventoryItem;
use Vendor\FilamentAccountingIr\Policies\Concerns\HandlesAccountingPermissions;

class InventoryItemPolicy
{
    use HandlesAccountingPermissions;

    public function viewAny(): bool
    {
        return $this->allow('accounting.inventory.view');
    }

    public function view(InventoryItem $record): bool
    {
        return $this->allow('accounting.inventory.view', $record);
    }

    public function create(): bool
    {
        return $this->allow('accounting.inventory.manage');
    }

    public function update(InventoryItem $record): bool
    {
        return $this->allow('accounting.inventory.manage', $record);
    }

    public function delete(InventoryItem $record): bool
    {
        return $this->allow('accounting.inventory.manage', $record);
    }

    public function restore(InventoryItem $record): bool
    {
        return $this->allow('accounting.inventory.manage', $record);
    }

    public function forceDelete(InventoryItem $record): bool
    {
        return $this->allow('accounting.inventory.manage', $record);
    }
}
