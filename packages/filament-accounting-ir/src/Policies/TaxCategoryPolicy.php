<?php

namespace Vendor\FilamentAccountingIr\Policies;

use Vendor\FilamentAccountingIr\Models\TaxCategory;
use Vendor\FilamentAccountingIr\Policies\Concerns\HandlesAccountingPermissions;

class TaxCategoryPolicy
{
    use HandlesAccountingPermissions;

    public function viewAny(): bool
    {
        return $this->allow('accounting.tax_category.view');
    }

    public function view(TaxCategory $record): bool
    {
        return $this->allow('accounting.tax_category.view', $record);
    }

    public function create(): bool
    {
        return $this->allow('accounting.tax_category.manage');
    }

    public function update(TaxCategory $record): bool
    {
        return $this->allow('accounting.tax_category.manage', $record);
    }

    public function delete(TaxCategory $record): bool
    {
        return $this->allow('accounting.tax_category.manage', $record);
    }

    public function restore(TaxCategory $record): bool
    {
        return $this->allow('accounting.tax_category.manage', $record);
    }

    public function forceDelete(TaxCategory $record): bool
    {
        return $this->allow('accounting.tax_category.manage', $record);
    }
}
