<?php

namespace Vendor\FilamentAccountingIr\Policies;

use Vendor\FilamentAccountingIr\Models\VatReport;
use Vendor\FilamentAccountingIr\Policies\Concerns\HandlesAccountingPermissions;

class VatReportPolicy
{
    use HandlesAccountingPermissions;

    public function viewAny(): bool
    {
        return $this->allow('accounting.vat_report.view');
    }

    public function view(VatReport $record): bool
    {
        return $this->allow('accounting.vat_report.view', $record);
    }

    public function create(): bool
    {
        return $this->allow('accounting.vat_report.manage');
    }

    public function update(VatReport $record): bool
    {
        return $this->allow('accounting.vat_report.manage', $record);
    }

    public function delete(VatReport $record): bool
    {
        return $this->allow('accounting.vat_report.manage', $record);
    }

    public function restore(VatReport $record): bool
    {
        return $this->allow('accounting.vat_report.manage', $record);
    }

    public function forceDelete(VatReport $record): bool
    {
        return $this->allow('accounting.vat_report.manage', $record);
    }
}
