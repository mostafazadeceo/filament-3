<?php

namespace Vendor\FilamentPayrollAttendanceIr;

use Filamat\IamSuite\Contracts\CapabilityRegistryInterface;
use Filamat\IamSuite\Support\IamAuthorization;
use Illuminate\Support\Facades\Gate;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Vendor\FilamentPayrollAttendanceIr\Models\PayrollAdvance;
use Vendor\FilamentPayrollAttendanceIr\Models\PayrollAllowanceTable;
use Vendor\FilamentPayrollAttendanceIr\Models\PayrollAttendanceRecord;
use Vendor\FilamentPayrollAttendanceIr\Models\PayrollAttendanceSchedule;
use Vendor\FilamentPayrollAttendanceIr\Models\PayrollAttendanceShift;
use Vendor\FilamentPayrollAttendanceIr\Models\PayrollContract;
use Vendor\FilamentPayrollAttendanceIr\Models\PayrollEmployee;
use Vendor\FilamentPayrollAttendanceIr\Models\PayrollInsuranceTable;
use Vendor\FilamentPayrollAttendanceIr\Models\PayrollLeaveRequest;
use Vendor\FilamentPayrollAttendanceIr\Models\PayrollLeaveType;
use Vendor\FilamentPayrollAttendanceIr\Models\PayrollLoan;
use Vendor\FilamentPayrollAttendanceIr\Models\PayrollMinimumWageTable;
use Vendor\FilamentPayrollAttendanceIr\Models\PayrollRun;
use Vendor\FilamentPayrollAttendanceIr\Models\PayrollSlip;
use Vendor\FilamentPayrollAttendanceIr\Models\PayrollTaxTable;
use Vendor\FilamentPayrollAttendanceIr\Models\PayrollTimePunch;
use Vendor\FilamentPayrollAttendanceIr\Policies\PayrollAdvancePolicy;
use Vendor\FilamentPayrollAttendanceIr\Policies\PayrollAllowanceTablePolicy;
use Vendor\FilamentPayrollAttendanceIr\Policies\PayrollAttendanceRecordPolicy;
use Vendor\FilamentPayrollAttendanceIr\Policies\PayrollAttendanceSchedulePolicy;
use Vendor\FilamentPayrollAttendanceIr\Policies\PayrollAttendanceShiftPolicy;
use Vendor\FilamentPayrollAttendanceIr\Policies\PayrollContractPolicy;
use Vendor\FilamentPayrollAttendanceIr\Policies\PayrollEmployeePolicy;
use Vendor\FilamentPayrollAttendanceIr\Policies\PayrollInsuranceTablePolicy;
use Vendor\FilamentPayrollAttendanceIr\Policies\PayrollLeaveRequestPolicy;
use Vendor\FilamentPayrollAttendanceIr\Policies\PayrollLeaveTypePolicy;
use Vendor\FilamentPayrollAttendanceIr\Policies\PayrollLoanPolicy;
use Vendor\FilamentPayrollAttendanceIr\Policies\PayrollMinimumWageTablePolicy;
use Vendor\FilamentPayrollAttendanceIr\Policies\PayrollRunPolicy;
use Vendor\FilamentPayrollAttendanceIr\Policies\PayrollSlipPolicy;
use Vendor\FilamentPayrollAttendanceIr\Policies\PayrollTaxTablePolicy;
use Vendor\FilamentPayrollAttendanceIr\Policies\PayrollTimePunchPolicy;
use Vendor\FilamentPayrollAttendanceIr\Support\PayrollAttendanceCapabilities;

class FilamentPayrollAttendanceIrServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('filament-payroll-attendance-ir')
            ->hasConfigFile('filament-payroll-attendance-ir')
            ->hasRoutes('api')
            ->hasMigrations([
                '2025_12_30_010001_create_payroll_employee_tables',
                '2025_12_30_010002_create_payroll_attendance_tables',
                '2025_12_30_010003_create_payroll_run_tables',
                '2025_12_30_010004_create_payroll_compliance_tables',
                '2025_12_30_010005_create_payroll_audit_webhook_tables',
            ])
            ->runsMigrations();
    }

    public function packageBooted(): void
    {
        Gate::policy(PayrollEmployee::class, PayrollEmployeePolicy::class);
        Gate::policy(PayrollContract::class, PayrollContractPolicy::class);
        Gate::policy(PayrollAttendanceShift::class, PayrollAttendanceShiftPolicy::class);
        Gate::policy(PayrollAttendanceSchedule::class, PayrollAttendanceSchedulePolicy::class);
        Gate::policy(PayrollTimePunch::class, PayrollTimePunchPolicy::class);
        Gate::policy(PayrollAttendanceRecord::class, PayrollAttendanceRecordPolicy::class);
        Gate::policy(PayrollLeaveType::class, PayrollLeaveTypePolicy::class);
        Gate::policy(PayrollLeaveRequest::class, PayrollLeaveRequestPolicy::class);
        Gate::policy(PayrollRun::class, PayrollRunPolicy::class);
        Gate::policy(PayrollSlip::class, PayrollSlipPolicy::class);
        Gate::policy(PayrollLoan::class, PayrollLoanPolicy::class);
        Gate::policy(PayrollAdvance::class, PayrollAdvancePolicy::class);
        Gate::policy(PayrollMinimumWageTable::class, PayrollMinimumWageTablePolicy::class);
        Gate::policy(PayrollAllowanceTable::class, PayrollAllowanceTablePolicy::class);
        Gate::policy(PayrollInsuranceTable::class, PayrollInsuranceTablePolicy::class);
        Gate::policy(PayrollTaxTable::class, PayrollTaxTablePolicy::class);

        if (class_exists(CapabilityRegistryInterface::class)) {
            $registry = $this->app->make(CapabilityRegistryInterface::class);
            PayrollAttendanceCapabilities::register($registry);
        }

        Gate::define('payroll.view', fn () => IamAuthorization::allows('payroll.view'));
    }
}
