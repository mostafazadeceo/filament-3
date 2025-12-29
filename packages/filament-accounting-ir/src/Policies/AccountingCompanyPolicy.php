<?php

namespace Vendor\FilamentAccountingIr\Policies;

use Vendor\FilamentAccountingIr\Models\AccountingCompany;
use Vendor\FilamentAccountingIr\Policies\Concerns\HandlesAccountingPermissions;

class AccountingCompanyPolicy
{
    use HandlesAccountingPermissions;

    public function viewAny(): bool
    {
        return $this->allow('accounting.company.view');
    }

    public function view(AccountingCompany $company): bool
    {
        return $this->allow('accounting.company.view', $company);
    }

    public function create(): bool
    {
        return $this->allow('accounting.company.manage');
    }

    public function update(AccountingCompany $company): bool
    {
        return $this->allow('accounting.company.manage', $company);
    }

    public function delete(AccountingCompany $company): bool
    {
        return $this->allow('accounting.company.manage', $company);
    }

    public function restore(AccountingCompany $company): bool
    {
        return $this->allow('accounting.company.manage', $company);
    }

    public function forceDelete(AccountingCompany $company): bool
    {
        return $this->allow('accounting.company.manage', $company);
    }
}
