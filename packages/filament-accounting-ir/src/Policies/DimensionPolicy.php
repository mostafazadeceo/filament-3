<?php

namespace Vendor\FilamentAccountingIr\Policies;

use Vendor\FilamentAccountingIr\Models\Dimension;
use Vendor\FilamentAccountingIr\Policies\Concerns\HandlesAccountingPermissions;

class DimensionPolicy
{
    use HandlesAccountingPermissions;

    public function viewAny(): bool
    {
        return $this->allow('accounting.dimension.view');
    }

    public function view(Dimension $dimension): bool
    {
        return $this->allow('accounting.dimension.view', $dimension);
    }

    public function create(): bool
    {
        return $this->allow('accounting.dimension.manage');
    }

    public function update(Dimension $dimension): bool
    {
        return $this->allow('accounting.dimension.manage', $dimension);
    }

    public function delete(Dimension $dimension): bool
    {
        return $this->allow('accounting.dimension.manage', $dimension);
    }
}
