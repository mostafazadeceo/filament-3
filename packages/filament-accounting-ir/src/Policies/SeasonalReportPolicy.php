<?php

namespace Vendor\FilamentAccountingIr\Policies;

use Vendor\FilamentAccountingIr\Models\SeasonalReport;
use Vendor\FilamentAccountingIr\Policies\Concerns\HandlesAccountingPermissions;

class SeasonalReportPolicy
{
    use HandlesAccountingPermissions;

    public function viewAny(): bool
    {
        return $this->allow('accounting.seasonal_report.view');
    }

    public function view(SeasonalReport $record): bool
    {
        return $this->allow('accounting.seasonal_report.view', $record);
    }

    public function create(): bool
    {
        return $this->allow('accounting.seasonal_report.manage');
    }

    public function update(SeasonalReport $record): bool
    {
        return $this->allow('accounting.seasonal_report.manage', $record);
    }

    public function delete(SeasonalReport $record): bool
    {
        return $this->allow('accounting.seasonal_report.manage', $record);
    }

    public function restore(SeasonalReport $record): bool
    {
        return $this->allow('accounting.seasonal_report.manage', $record);
    }

    public function forceDelete(SeasonalReport $record): bool
    {
        return $this->allow('accounting.seasonal_report.manage', $record);
    }
}
