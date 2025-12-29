<?php

namespace Vendor\FilamentPayrollAttendanceIr\Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Vendor\FilamentAccountingIr\Models\AccountingCompany;
use Vendor\FilamentPayrollAttendanceIr\Models\PayrollAllowanceTable;
use Vendor\FilamentPayrollAttendanceIr\Models\PayrollInsuranceTable;
use Vendor\FilamentPayrollAttendanceIr\Models\PayrollMinimumWageTable;
use Vendor\FilamentPayrollAttendanceIr\Models\PayrollTaxBracket;
use Vendor\FilamentPayrollAttendanceIr\Models\PayrollTaxTable;

class PayrollComplianceSeeder extends Seeder
{
    public function run(): void
    {
        $effectiveFrom = Carbon::create(2025, 3, 21);
        $dailyWage = 3_463_656;
        $monthlyWage = 103_909_680;
        $housingAllowance = 9_000_000;
        $foodAllowance = 22_000_000;
        $marriageAllowance = 5_000_000;
        $childAllowanceDaily = round($dailyWage * 0.1, 2);
        $seniorityAllowanceDaily = 282_000;
        $maxInsurableDaily = $dailyWage * 7;
        $maxInsurableMonthly = $maxInsurableDaily * 30;
        $taxExemption = 240_000_000;
        $flatAllowanceRate = 10.00;

        $brackets = [
            ['min' => 0, 'max' => 60_000_000, 'rate' => 10.00],
            ['min' => 60_000_000, 'max' => 140_000_000, 'rate' => 15.00],
            ['min' => 140_000_000, 'max' => 260_000_000, 'rate' => 20.00],
            ['min' => 260_000_000, 'max' => 426_666_667, 'rate' => 25.00],
            ['min' => 426_666_667, 'max' => null, 'rate' => 30.00],
        ];

        AccountingCompany::query()
            ->where('is_active', true)
            ->get(['id', 'tenant_id'])
            ->each(function (AccountingCompany $company) use (
                $effectiveFrom,
                $dailyWage,
                $monthlyWage,
                $housingAllowance,
                $foodAllowance,
                $marriageAllowance,
                $childAllowanceDaily,
                $seniorityAllowanceDaily,
                $maxInsurableDaily,
                $maxInsurableMonthly,
                $taxExemption,
                $flatAllowanceRate,
                $brackets
            ): void {
                PayrollMinimumWageTable::query()->updateOrCreate([
                    'company_id' => $company->id,
                    'effective_from' => $effectiveFrom,
                ], [
                    'tenant_id' => $company->tenant_id,
                    'effective_to' => null,
                    'daily_wage' => $dailyWage,
                    'monthly_wage' => $monthlyWage,
                    'description' => '1404 baseline',
                ]);

                PayrollAllowanceTable::query()->updateOrCreate([
                    'company_id' => $company->id,
                    'effective_from' => $effectiveFrom,
                ], [
                    'tenant_id' => $company->tenant_id,
                    'effective_to' => null,
                    'housing_allowance' => $housingAllowance,
                    'food_allowance' => $foodAllowance,
                    'child_allowance_daily' => $childAllowanceDaily,
                    'marriage_allowance' => $marriageAllowance,
                    'seniority_allowance_daily' => $seniorityAllowanceDaily,
                    'description' => '1404 baseline',
                ]);

                PayrollInsuranceTable::query()->updateOrCreate([
                    'company_id' => $company->id,
                    'effective_from' => $effectiveFrom,
                ], [
                    'tenant_id' => $company->tenant_id,
                    'effective_to' => null,
                    'employee_rate' => 7.00,
                    'employer_rate' => 23.00,
                    'max_insurable_daily' => $maxInsurableDaily,
                    'max_insurable_monthly' => $maxInsurableMonthly,
                    'description' => '1404 baseline',
                ]);

                $taxTable = PayrollTaxTable::query()->updateOrCreate([
                    'company_id' => $company->id,
                    'effective_from' => $effectiveFrom,
                ], [
                    'tenant_id' => $company->tenant_id,
                    'effective_to' => null,
                    'exemption_amount' => $taxExemption,
                    'flat_allowance_rate' => $flatAllowanceRate,
                    'description' => '1404 baseline',
                ]);

                foreach ($brackets as $bracket) {
                    PayrollTaxBracket::query()->updateOrCreate([
                        'payroll_tax_table_id' => $taxTable->id,
                        'min_amount' => $bracket['min'],
                        'max_amount' => $bracket['max'],
                    ], [
                        'tenant_id' => $company->tenant_id,
                        'company_id' => $company->id,
                        'rate' => $bracket['rate'],
                    ]);
                }
            });
    }
}
