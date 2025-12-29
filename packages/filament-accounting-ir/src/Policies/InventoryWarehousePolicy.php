<?php

namespace Vendor\FilamentAccountingIr\Policies;

use Vendor\FilamentAccountingIr\Models\InventoryWarehouse;
use Vendor\FilamentAccountingIr\Policies\Concerns\HandlesAccountingPermissions;

class InventoryWarehousePolicy
{
    use HandlesAccountingPermissions;

    public function viewAny(): bool
    {
        return $this->allow('accounting.inventory.view');
    }

    public function view(InventoryWarehouse $warehouse): bool
    {
        return $this->allow('accounting.inventory.view', $warehouse);
    }

    public function create(): bool
    {
        return $this->allow('accounting.inventory.manage');
    }

    public function update(InventoryWarehouse $warehouse): bool
    {
        return $this->allow('accounting.inventory.manage', $warehouse);
    }

    public function delete(InventoryWarehouse $warehouse): bool
    {
        return $this->allow('accounting.inventory.manage', $warehouse);
    }
}
