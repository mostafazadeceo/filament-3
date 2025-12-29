<?php

namespace Vendor\FilamentAccountingIr\Policies;

use Vendor\FilamentAccountingIr\Models\TaxRate;
use Vendor\FilamentAccountingIr\Policies\Concerns\HandlesAccountingPermissions;

class TaxRatePolicy
{
    use HandlesAccountingPermissions;

    public function viewAny(): bool
    {
        return $this->allow('accounting.tax_rate.view');
    }

    public function view(TaxRate $record): bool
    {
        return $this->allow('accounting.tax_rate.view', $record);
    }

    public function create(): bool
    {
        return $this->allow('accounting.tax_rate.manage');
    }

    public function update(TaxRate $record): bool
    {
        return $this->allow('accounting.tax_rate.manage', $record);
    }

    public function delete(TaxRate $record): bool
    {
        return $this->allow('accounting.tax_rate.manage', $record);
    }

    public function restore(TaxRate $record): bool
    {
        return $this->allow('accounting.tax_rate.manage', $record);
    }

    public function forceDelete(TaxRate $record): bool
    {
        return $this->allow('accounting.tax_rate.manage', $record);
    }
}
