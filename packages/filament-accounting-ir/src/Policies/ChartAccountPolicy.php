<?php

namespace Vendor\FilamentAccountingIr\Policies;

use Vendor\FilamentAccountingIr\Models\ChartAccount;
use Vendor\FilamentAccountingIr\Policies\Concerns\HandlesAccountingPermissions;

class ChartAccountPolicy
{
    use HandlesAccountingPermissions;

    public function viewAny(): bool
    {
        return $this->allow('accounting.chart_account.view');
    }

    public function view(ChartAccount $account): bool
    {
        return $this->allow('accounting.chart_account.view', $account);
    }

    public function create(): bool
    {
        return $this->allow('accounting.chart_account.manage');
    }

    public function update(ChartAccount $account): bool
    {
        return $this->allow('accounting.chart_account.manage', $account);
    }

    public function delete(ChartAccount $account): bool
    {
        return $this->allow('accounting.chart_account.manage', $account);
    }

    public function restore(ChartAccount $account): bool
    {
        return $this->allow('accounting.chart_account.manage', $account);
    }

    public function forceDelete(ChartAccount $account): bool
    {
        return $this->allow('accounting.chart_account.manage', $account);
    }
}
