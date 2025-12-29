<?php

namespace Vendor\FilamentAccountingIr\Policies;

use Vendor\FilamentAccountingIr\Models\AccountingCompanySetting;
use Vendor\FilamentAccountingIr\Policies\Concerns\HandlesAccountingPermissions;

class AccountingCompanySettingPolicy
{
    use HandlesAccountingPermissions;

    public function viewAny(): bool
    {
        return $this->allow('accounting.company_settings.view');
    }

    public function view(AccountingCompanySetting $setting): bool
    {
        return $this->allow('accounting.company_settings.view', $setting);
    }

    public function create(): bool
    {
        return $this->allow('accounting.company_settings.manage');
    }

    public function update(AccountingCompanySetting $setting): bool
    {
        return $this->allow('accounting.company_settings.manage', $setting);
    }

    public function delete(AccountingCompanySetting $setting): bool
    {
        return $this->allow('accounting.company_settings.manage', $setting);
    }

    public function restore(AccountingCompanySetting $setting): bool
    {
        return $this->allow('accounting.company_settings.manage', $setting);
    }

    public function forceDelete(AccountingCompanySetting $setting): bool
    {
        return $this->allow('accounting.company_settings.manage', $setting);
    }
}
