<?php

namespace Vendor\FilamentAccountingIr\Policies;

use Vendor\FilamentAccountingIr\Models\InventoryDoc;
use Vendor\FilamentAccountingIr\Policies\Concerns\HandlesAccountingPermissions;

class InventoryDocPolicy
{
    use HandlesAccountingPermissions;

    public function viewAny(): bool
    {
        return $this->allow('accounting.inventory.view');
    }

    public function view(InventoryDoc $record): bool
    {
        return $this->allow('accounting.inventory.view', $record);
    }

    public function create(): bool
    {
        return $this->allow('accounting.inventory.manage');
    }

    public function update(InventoryDoc $record): bool
    {
        return $record->status !== 'posted'
            && $this->allow('accounting.inventory.manage', $record);
    }

    public function delete(InventoryDoc $record): bool
    {
        return $record->status !== 'posted'
            && $this->allow('accounting.inventory.manage', $record);
    }

    public function restore(InventoryDoc $record): bool
    {
        return $this->allow('accounting.inventory.manage', $record);
    }

    public function forceDelete(InventoryDoc $record): bool
    {
        return $record->status !== 'posted'
            && $this->allow('accounting.inventory.manage', $record);
    }

    public function post(InventoryDoc $record): bool
    {
        return $this->allow('accounting.inventory.post', $record)
            || $this->allow('accounting.inventory.manage', $record);
    }
}
