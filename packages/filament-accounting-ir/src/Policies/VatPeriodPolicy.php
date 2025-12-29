<?php

namespace Vendor\FilamentAccountingIr\Policies;

use Vendor\FilamentAccountingIr\Models\VatPeriod;
use Vendor\FilamentAccountingIr\Policies\Concerns\HandlesAccountingPermissions;

class VatPeriodPolicy
{
    use HandlesAccountingPermissions;

    public function viewAny(): bool
    {
        return $this->allow('accounting.vat_period.view');
    }

    public function view(VatPeriod $record): bool
    {
        return $this->allow('accounting.vat_period.view', $record);
    }

    public function create(): bool
    {
        return $this->allow('accounting.vat_period.manage');
    }

    public function update(VatPeriod $record): bool
    {
        return $this->allow('accounting.vat_period.manage', $record);
    }

    public function delete(VatPeriod $record): bool
    {
        return $this->allow('accounting.vat_period.manage', $record);
    }

    public function restore(VatPeriod $record): bool
    {
        return $this->allow('accounting.vat_period.manage', $record);
    }

    public function forceDelete(VatPeriod $record): bool
    {
        return $this->allow('accounting.vat_period.manage', $record);
    }
}
