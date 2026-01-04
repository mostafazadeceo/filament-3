<?php

namespace Vendor\FilamentPayrollAttendanceIr;

use Filamat\IamSuite\Contracts\CapabilityRegistryInterface;
use Filamat\IamSuite\Support\IamAuthorization;
use Illuminate\Support\Facades\Gate;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Vendor\FilamentPayrollAttendanceIr\Domain\Models\AttendanceException;
use Vendor\FilamentPayrollAttendanceIr\Domain\Models\AttendancePolicy;
use Vendor\FilamentPayrollAttendanceIr\Domain\Models\EmployeeConsent;
use Vendor\FilamentPayrollAttendanceIr\Domain\Models\HolidayRule;
use Vendor\FilamentPayrollAttendanceIr\Domain\Models\OvertimeRequest;
use Vendor\FilamentPayrollAttendanceIr\Domain\Models\SensitiveAccessLog;
use Vendor\FilamentPayrollAttendanceIr\Domain\Models\TimeEvent;
use Vendor\FilamentPayrollAttendanceIr\Domain\Models\Timesheet;
use Vendor\FilamentPayrollAttendanceIr\Domain\Models\WorkCalendar;
use Vendor\FilamentPayrollAttendanceIr\Models\PayrollAdvance;
use Vendor\FilamentPayrollAttendanceIr\Models\PayrollAiLog;
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
use Vendor\FilamentPayrollAttendanceIr\Models\PayrollMission;
use Vendor\FilamentPayrollAttendanceIr\Models\PayrollRun;
use Vendor\FilamentPayrollAttendanceIr\Models\PayrollSlip;
use Vendor\FilamentPayrollAttendanceIr\Models\PayrollTaxTable;
use Vendor\FilamentPayrollAttendanceIr\Models\PayrollTimePunch;
use Vendor\FilamentPayrollAttendanceIr\Policies\EmployeeConsentPolicy;
use Vendor\FilamentPayrollAttendanceIr\Policies\HolidayRulePolicy;
use Vendor\FilamentPayrollAttendanceIr\Policies\MissionRequestPolicy;
use Vendor\FilamentPayrollAttendanceIr\Policies\OvertimeRequestPolicy;
use Vendor\FilamentPayrollAttendanceIr\Policies\PayrollAdvancePolicy;
use Vendor\FilamentPayrollAttendanceIr\Policies\PayrollAiLogPolicy;
use Vendor\FilamentPayrollAttendanceIr\Policies\PayrollAllowanceTablePolicy;
use Vendor\FilamentPayrollAttendanceIr\Policies\PayrollAttendanceExceptionPolicy;
use Vendor\FilamentPayrollAttendanceIr\Policies\PayrollAttendancePolicyPolicy;
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
use Vendor\FilamentPayrollAttendanceIr\Policies\PayrollTimeEventPolicy;
use Vendor\FilamentPayrollAttendanceIr\Policies\PayrollTimePunchPolicy;
use Vendor\FilamentPayrollAttendanceIr\Policies\SensitiveAccessLogPolicy;
use Vendor\FilamentPayrollAttendanceIr\Policies\TimesheetPolicy;
use Vendor\FilamentPayrollAttendanceIr\Policies\WorkCalendarPolicy;
use Vendor\FilamentPayrollAttendanceIr\Support\PayrollAttendanceCapabilities;

class FilamentPayrollAttendanceIrServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('filament-payroll-attendance-ir')
            ->hasConfigFile('filament-payroll-attendance-ir')
            ->hasViews()
            ->hasRoutes('api')
            ->hasMigrations([
                '2025_12_30_010001_create_payroll_employee_tables',
                '2025_12_30_010002_create_payroll_attendance_tables',
                '2025_12_30_010003_create_payroll_run_tables',
                '2025_12_30_010004_create_payroll_compliance_tables',
                '2025_12_30_010005_create_payroll_audit_webhook_tables',
                '2025_12_30_010006_create_payroll_org_tables',
                '2025_12_30_010007_create_payroll_policy_calendar_tables',
                '2025_12_30_010008_create_payroll_time_tracking_tables',
                '2025_12_30_010009_create_payroll_privacy_tables',
                '2025_12_30_010010_create_payroll_ai_logs_table',
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
        Gate::policy(AttendanceException::class, PayrollAttendanceExceptionPolicy::class);
        Gate::policy(AttendancePolicy::class, PayrollAttendancePolicyPolicy::class);
        Gate::policy(TimeEvent::class, PayrollTimeEventPolicy::class);
        Gate::policy(Timesheet::class, TimesheetPolicy::class);
        Gate::policy(OvertimeRequest::class, OvertimeRequestPolicy::class);
        Gate::policy(PayrollMission::class, MissionRequestPolicy::class);
        Gate::policy(WorkCalendar::class, WorkCalendarPolicy::class);
        Gate::policy(HolidayRule::class, HolidayRulePolicy::class);
        Gate::policy(EmployeeConsent::class, EmployeeConsentPolicy::class);
        Gate::policy(SensitiveAccessLog::class, SensitiveAccessLogPolicy::class);
        Gate::policy(PayrollAiLog::class, PayrollAiLogPolicy::class);

        if (class_exists(CapabilityRegistryInterface::class)) {
            $registry = $this->app->make(CapabilityRegistryInterface::class);
            PayrollAttendanceCapabilities::register($registry);
        }

        Gate::define('payroll.view', fn () => IamAuthorization::allows('payroll.view'));
    }
}
