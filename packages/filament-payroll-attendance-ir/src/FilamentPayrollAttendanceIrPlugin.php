<?php

namespace Vendor\FilamentPayrollAttendanceIr;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Vendor\FilamentPayrollAttendanceIr\Filament\Pages\AttendanceManagementReportsPage;
use Vendor\FilamentPayrollAttendanceIr\Filament\Resources\AttendancePolicyResource;
use Vendor\FilamentPayrollAttendanceIr\Filament\Resources\EmployeeConsentResource;
use Vendor\FilamentPayrollAttendanceIr\Filament\Resources\MissionRequestResource;
use Vendor\FilamentPayrollAttendanceIr\Filament\Resources\OvertimeRequestResource;
use Vendor\FilamentPayrollAttendanceIr\Filament\Resources\PayrollAdvanceResource;
use Vendor\FilamentPayrollAttendanceIr\Filament\Resources\PayrollAiLogResource;
use Vendor\FilamentPayrollAttendanceIr\Filament\Resources\PayrollAllowanceTableResource;
use Vendor\FilamentPayrollAttendanceIr\Filament\Resources\PayrollAttendanceExceptionResource;
use Vendor\FilamentPayrollAttendanceIr\Filament\Resources\PayrollAttendanceRecordResource;
use Vendor\FilamentPayrollAttendanceIr\Filament\Resources\PayrollAttendanceScheduleResource;
use Vendor\FilamentPayrollAttendanceIr\Filament\Resources\PayrollAttendanceShiftResource;
use Vendor\FilamentPayrollAttendanceIr\Filament\Resources\PayrollContractResource;
use Vendor\FilamentPayrollAttendanceIr\Filament\Resources\PayrollEmployeeResource;
use Vendor\FilamentPayrollAttendanceIr\Filament\Resources\PayrollInsuranceTableResource;
use Vendor\FilamentPayrollAttendanceIr\Filament\Resources\PayrollLeaveRequestResource;
use Vendor\FilamentPayrollAttendanceIr\Filament\Resources\PayrollLeaveTypeResource;
use Vendor\FilamentPayrollAttendanceIr\Filament\Resources\PayrollLoanResource;
use Vendor\FilamentPayrollAttendanceIr\Filament\Resources\PayrollMinimumWageTableResource;
use Vendor\FilamentPayrollAttendanceIr\Filament\Resources\PayrollRunResource;
use Vendor\FilamentPayrollAttendanceIr\Filament\Resources\PayrollSlipResource;
use Vendor\FilamentPayrollAttendanceIr\Filament\Resources\PayrollTaxTableResource;
use Vendor\FilamentPayrollAttendanceIr\Filament\Resources\PayrollTimePunchResource;
use Vendor\FilamentPayrollAttendanceIr\Filament\Resources\SensitiveAccessLogResource;
use Vendor\FilamentPayrollAttendanceIr\Filament\Resources\TimeEventResource;
use Vendor\FilamentPayrollAttendanceIr\Filament\Resources\TimesheetResource;
use Vendor\FilamentPayrollAttendanceIr\Filament\Resources\WorkCalendarResource;

class FilamentPayrollAttendanceIrPlugin implements Plugin
{
    public static function make(): static
    {
        return app(static::class);
    }

    public function getId(): string
    {
        return 'payroll-attendance';
    }

    public function register(Panel $panel): void
    {
        $panel->resources([
            PayrollEmployeeResource::class,
            PayrollContractResource::class,
            PayrollAttendanceShiftResource::class,
            PayrollAttendanceScheduleResource::class,
            PayrollTimePunchResource::class,
            PayrollAttendanceRecordResource::class,
            TimeEventResource::class,
            PayrollAttendanceExceptionResource::class,
            AttendancePolicyResource::class,
            WorkCalendarResource::class,
            TimesheetResource::class,
            PayrollLeaveTypeResource::class,
            PayrollLeaveRequestResource::class,
            MissionRequestResource::class,
            OvertimeRequestResource::class,
            EmployeeConsentResource::class,
            SensitiveAccessLogResource::class,
            PayrollAiLogResource::class,
            PayrollRunResource::class,
            PayrollSlipResource::class,
            PayrollLoanResource::class,
            PayrollAdvanceResource::class,
            PayrollMinimumWageTableResource::class,
            PayrollAllowanceTableResource::class,
            PayrollInsuranceTableResource::class,
            PayrollTaxTableResource::class,
        ]);

        $panel->pages([
            AttendanceManagementReportsPage::class,
        ]);
    }

    public function boot(Panel $panel): void
    {
        // No-op.
    }
}
