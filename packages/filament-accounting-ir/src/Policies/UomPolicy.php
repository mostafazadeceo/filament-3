<?php

namespace Vendor\FilamentAccountingIr\Policies;

use Vendor\FilamentAccountingIr\Models\Uom;
use Vendor\FilamentAccountingIr\Policies\Concerns\HandlesAccountingPermissions;

class UomPolicy
{
    use HandlesAccountingPermissions;

    public function viewAny(): bool
    {
        return $this->allow('accounting.uom.view');
    }

    public function view(Uom $record): bool
    {
        return $this->allow('accounting.uom.view', $record);
    }

    public function create(): bool
    {
        return $this->allow('accounting.uom.manage');
    }

    public function update(Uom $record): bool
    {
        return $this->allow('accounting.uom.manage', $record);
    }

    public function delete(Uom $record): bool
    {
        return $this->allow('accounting.uom.manage', $record);
    }

    public function restore(Uom $record): bool
    {
        return $this->allow('accounting.uom.manage', $record);
    }

    public function forceDelete(Uom $record): bool
    {
        return $this->allow('accounting.uom.manage', $record);
    }
}
