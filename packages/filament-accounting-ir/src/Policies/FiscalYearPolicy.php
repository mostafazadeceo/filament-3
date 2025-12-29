<?php

namespace Vendor\FilamentAccountingIr\Policies;

use Vendor\FilamentAccountingIr\Models\FiscalYear;
use Vendor\FilamentAccountingIr\Policies\Concerns\HandlesAccountingPermissions;

class FiscalYearPolicy
{
    use HandlesAccountingPermissions;

    public function viewAny(): bool
    {
        return $this->allow('accounting.fiscal_year.view');
    }

    public function view(FiscalYear $year): bool
    {
        return $this->allow('accounting.fiscal_year.view', $year);
    }

    public function create(): bool
    {
        return $this->allow('accounting.fiscal_year.manage');
    }

    public function update(FiscalYear $year): bool
    {
        return $this->allow('accounting.fiscal_year.manage', $year);
    }

    public function delete(FiscalYear $year): bool
    {
        return $this->allow('accounting.fiscal_year.manage', $year);
    }
}
