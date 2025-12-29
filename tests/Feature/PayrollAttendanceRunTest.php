<?php

namespace Tests\Feature;

use Filamat\IamSuite\Models\Tenant;
use Filamat\IamSuite\Support\TenantContext;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Vendor\FilamentAccountingIr\Models\AccountingBranch;
use Vendor\FilamentAccountingIr\Models\AccountingCompany;
use Vendor\FilamentPayrollAttendanceIr\Models\PayrollAllowanceTable;
use Vendor\FilamentPayrollAttendanceIr\Models\PayrollAttendanceRecord;
use Vendor\FilamentPayrollAttendanceIr\Models\PayrollContract;
use Vendor\FilamentPayrollAttendanceIr\Models\PayrollEmployee;
use Vendor\FilamentPayrollAttendanceIr\Models\PayrollInsuranceTable;
use Vendor\FilamentPayrollAttendanceIr\Models\PayrollRun;
use Vendor\FilamentPayrollAttendanceIr\Models\PayrollTaxBracket;
use Vendor\FilamentPayrollAttendanceIr\Models\PayrollTaxTable;
use Vendor\FilamentPayrollAttendanceIr\Services\PayrollRunService;

class PayrollAttendanceRunTest extends \Tests\TestCase
{
    use RefreshDatabase;

    public function test_payroll_run_generates_slips(): void
    {
        $tenant = Tenant::query()->create([
            'name' => 'Payroll Tenant',
            'slug' => 'payroll-tenant',
        ]);
        TenantContext::setTenant($tenant);

        $company = AccountingCompany::query()->create([
            'name' => 'Payroll Company',
        ]);

        $branch = AccountingBranch::query()->create([
            'company_id' => $company->getKey(),
            'name' => 'Main Branch',
        ]);

        $employee = PayrollEmployee::query()->create([
            'company_id' => $company->getKey(),
            'branch_id' => $branch->getKey(),
            'first_name' => 'Ali',
            'last_name' => 'Karimi',
            'marital_status' => 'married',
            'children_count' => 1,
            'employment_date' => now()->subYears(2)->toDateString(),
        ]);

        PayrollContract::query()->create([
            'company_id' => $company->getKey(),
            'branch_id' => $branch->getKey(),
            'employee_id' => $employee->getKey(),
            'scope' => 'official',
            'status' => 'active',
            'effective_from' => now()->subMonth()->toDateString(),
            'base_salary' => 100000000,
        ]);

        PayrollAllowanceTable::query()->create([
            'company_id' => $company->getKey(),
            'effective_from' => now()->subMonth()->toDateString(),
            'housing_allowance' => 9000000,
            'food_allowance' => 22000000,
            'child_allowance_daily' => 300000,
            'marriage_allowance' => 5000000,
            'seniority_allowance_daily' => 100000,
        ]);

        PayrollInsuranceTable::query()->create([
            'company_id' => $company->getKey(),
            'effective_from' => now()->subMonth()->toDateString(),
            'employee_rate' => 7,
            'employer_rate' => 23,
            'max_insurable_monthly' => 300000000,
        ]);

        $taxTable = PayrollTaxTable::query()->create([
            'company_id' => $company->getKey(),
            'effective_from' => now()->subMonth()->toDateString(),
            'exemption_amount' => 20000000,
            'flat_allowance_rate' => 10,
        ]);

        PayrollTaxBracket::query()->create([
            'company_id' => $company->getKey(),
            'payroll_tax_table_id' => $taxTable->getKey(),
            'min_amount' => 0,
            'max_amount' => 50000000,
            'rate' => 10,
        ]);

        PayrollAttendanceRecord::query()->create([
            'company_id' => $company->getKey(),
            'branch_id' => $branch->getKey(),
            'employee_id' => $employee->getKey(),
            'work_date' => now()->toDateString(),
            'worked_minutes' => 480,
            'overtime_minutes' => 120,
            'status' => 'approved',
        ]);

        $run = PayrollRun::query()->create([
            'company_id' => $company->getKey(),
            'branch_id' => $branch->getKey(),
            'period_start' => now()->startOfMonth()->toDateString(),
            'period_end' => now()->endOfMonth()->toDateString(),
        ]);

        app(PayrollRunService::class)->generate($run);

        $slip = $run->slips()->where('employee_id', $employee->getKey())->first();

        $this->assertNotNull($slip);
        $this->assertGreaterThan(0, (float) $slip->gross_amount);
        $this->assertGreaterThan(0, $slip->items()->count());
    }
}
