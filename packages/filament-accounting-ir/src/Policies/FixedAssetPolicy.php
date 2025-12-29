<?php

namespace Vendor\FilamentAccountingIr\Policies;

use Vendor\FilamentAccountingIr\Models\FixedAsset;
use Vendor\FilamentAccountingIr\Policies\Concerns\HandlesAccountingPermissions;

class FixedAssetPolicy
{
    use HandlesAccountingPermissions;

    public function viewAny(): bool
    {
        return $this->allow('accounting.fixed_assets.view');
    }

    public function view(FixedAsset $asset): bool
    {
        return $this->allow('accounting.fixed_assets.view', $asset);
    }

    public function create(): bool
    {
        return $this->allow('accounting.fixed_assets.manage');
    }

    public function update(FixedAsset $asset): bool
    {
        return $this->allow('accounting.fixed_assets.manage', $asset);
    }

    public function delete(FixedAsset $asset): bool
    {
        return $this->allow('accounting.fixed_assets.manage', $asset);
    }
}
