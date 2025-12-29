<?php

namespace Haida\FilamentPayrollAttendance;

use Filamat\IamSuite\Contracts\CapabilityRegistryInterface;
use Filamat\IamSuite\Support\IamAuthorization;
use Haida\FilamentPayrollAttendance\Support\PayrollAttendanceCapabilities;
use Illuminate\Support\Facades\Gate;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class FilamentPayrollAttendanceServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('filament-payroll-attendance')
            ->hasConfigFile('filament-payroll-attendance')
            ->hasTranslations()
            ->hasRoutes('api')
            ->hasMigrations([
                '2025_12_30_000001_create_payroll_ir_employee_tables',
                '2025_12_30_000002_create_payroll_ir_contract_and_leave_tables',
                '2025_12_30_000003_create_payroll_ir_attendance_tables',
                '2025_12_30_000004_create_payroll_ir_payroll_tables',
                '2025_12_30_000005_create_payroll_ir_reference_tables',
                '2025_12_30_000006_create_payroll_ir_finance_tables',
                '2025_12_30_000007_create_payroll_ir_audit_tables',
            ])
            ->runsMigrations();
    }

    public function packageBooted(): void
    {
        if (class_exists(CapabilityRegistryInterface::class)) {
            $registry = $this->app->make(CapabilityRegistryInterface::class);
            PayrollAttendanceCapabilities::register($registry);
        }

        Gate::define('payroll.view', fn () => IamAuthorization::allows('payroll.view'));
    }
}
