<?php

namespace Vendor\FilamentAccountingIr\Policies;

use Vendor\FilamentAccountingIr\Models\FiscalPeriod;
use Vendor\FilamentAccountingIr\Policies\Concerns\HandlesAccountingPermissions;

class FiscalPeriodPolicy
{
    use HandlesAccountingPermissions;

    public function viewAny(): bool
    {
        return $this->allow('accounting.fiscal_year.view');
    }

    public function view(FiscalPeriod $period): bool
    {
        return $this->allow('accounting.fiscal_year.view', $period);
    }

    public function create(): bool
    {
        return $this->allow('accounting.fiscal_year.manage');
    }

    public function update(FiscalPeriod $period): bool
    {
        return $this->allow('accounting.fiscal_year.manage', $period);
    }

    public function delete(FiscalPeriod $period): bool
    {
        return $this->allow('accounting.fiscal_year.manage', $period);
    }
}
