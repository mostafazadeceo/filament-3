<?php

namespace Vendor\FilamentPayrollAttendanceIr\Services;

use Carbon\Carbon;
use Vendor\FilamentPayrollAttendanceIr\Models\PayrollAllowanceTable;
use Vendor\FilamentPayrollAttendanceIr\Models\PayrollInsuranceTable;
use Vendor\FilamentPayrollAttendanceIr\Models\PayrollMinimumWageTable;
use Vendor\FilamentPayrollAttendanceIr\Models\PayrollTaxTable;

class PayrollSettingsResolver
{
    public function resolveAllowanceTable(int $companyId, Carbon $date): ?PayrollAllowanceTable
    {
        return PayrollAllowanceTable::query()
            ->where('company_id', $companyId)
            ->where('effective_from', '<=', $date->toDateString())
            ->where(function ($query) use ($date): void {
                $query->whereNull('effective_to')
                    ->orWhere('effective_to', '>=', $date->toDateString());
            })
            ->orderByDesc('effective_from')
            ->first();
    }

    public function resolveInsuranceTable(int $companyId, Carbon $date): ?PayrollInsuranceTable
    {
        return PayrollInsuranceTable::query()
            ->where('company_id', $companyId)
            ->where('effective_from', '<=', $date->toDateString())
            ->where(function ($query) use ($date): void {
                $query->whereNull('effective_to')
                    ->orWhere('effective_to', '>=', $date->toDateString());
            })
            ->orderByDesc('effective_from')
            ->first();
    }

    public function resolveMinimumWageTable(int $companyId, Carbon $date): ?PayrollMinimumWageTable
    {
        return PayrollMinimumWageTable::query()
            ->where('company_id', $companyId)
            ->where('effective_from', '<=', $date->toDateString())
            ->where(function ($query) use ($date): void {
                $query->whereNull('effective_to')
                    ->orWhere('effective_to', '>=', $date->toDateString());
            })
            ->orderByDesc('effective_from')
            ->first();
    }

    public function resolveTaxTable(int $companyId, Carbon $date): ?PayrollTaxTable
    {
        return PayrollTaxTable::query()
            ->where('company_id', $companyId)
            ->where('effective_from', '<=', $date->toDateString())
            ->where(function ($query) use ($date): void {
                $query->whereNull('effective_to')
                    ->orWhere('effective_to', '>=', $date->toDateString());
            })
            ->with('brackets')
            ->orderByDesc('effective_from')
            ->first();
    }
}
