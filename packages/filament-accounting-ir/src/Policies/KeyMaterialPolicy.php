<?php

namespace Vendor\FilamentAccountingIr\Policies;

use Vendor\FilamentAccountingIr\Models\KeyMaterial;
use Vendor\FilamentAccountingIr\Policies\Concerns\HandlesAccountingPermissions;

class KeyMaterialPolicy
{
    use HandlesAccountingPermissions;

    public function viewAny(): bool
    {
        return $this->allow('accounting.einvoice.view');
    }

    public function view(KeyMaterial $record): bool
    {
        return $this->allow('accounting.einvoice.view', $record);
    }

    public function create(): bool
    {
        return $this->allow('accounting.einvoice.keys.manage');
    }

    public function update(KeyMaterial $record): bool
    {
        return $this->allow('accounting.einvoice.keys.manage', $record);
    }

    public function delete(KeyMaterial $record): bool
    {
        return $this->allow('accounting.einvoice.keys.manage', $record);
    }

    public function restore(KeyMaterial $record): bool
    {
        return $this->allow('accounting.einvoice.keys.manage', $record);
    }

    public function forceDelete(KeyMaterial $record): bool
    {
        return $this->allow('accounting.einvoice.keys.manage', $record);
    }
}
