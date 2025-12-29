<?php

namespace Vendor\FilamentAccountingIr\Policies;

use Vendor\FilamentAccountingIr\Models\WithholdingRate;
use Vendor\FilamentAccountingIr\Policies\Concerns\HandlesAccountingPermissions;

class WithholdingRatePolicy
{
    use HandlesAccountingPermissions;

    public function viewAny(): bool
    {
        return $this->allow('accounting.withholding_rate.view');
    }

    public function view(WithholdingRate $record): bool
    {
        return $this->allow('accounting.withholding_rate.view', $record);
    }

    public function create(): bool
    {
        return $this->allow('accounting.withholding_rate.manage');
    }

    public function update(WithholdingRate $record): bool
    {
        return $this->allow('accounting.withholding_rate.manage', $record);
    }

    public function delete(WithholdingRate $record): bool
    {
        return $this->allow('accounting.withholding_rate.manage', $record);
    }

    public function restore(WithholdingRate $record): bool
    {
        return $this->allow('accounting.withholding_rate.manage', $record);
    }

    public function forceDelete(WithholdingRate $record): bool
    {
        return $this->allow('accounting.withholding_rate.manage', $record);
    }
}
