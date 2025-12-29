<?php

namespace Vendor\FilamentPayrollAttendanceIr;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Vendor\FilamentPayrollAttendanceIr\Filament\Resources\PayrollAdvanceResource;
use Vendor\FilamentPayrollAttendanceIr\Filament\Resources\PayrollAllowanceTableResource;
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
            PayrollLeaveTypeResource::class,
            PayrollLeaveRequestResource::class,
            PayrollRunResource::class,
            PayrollSlipResource::class,
            PayrollLoanResource::class,
            PayrollAdvanceResource::class,
            PayrollMinimumWageTableResource::class,
            PayrollAllowanceTableResource::class,
            PayrollInsuranceTableResource::class,
            PayrollTaxTableResource::class,
        ]);
    }

    public function boot(Panel $panel): void
    {
        // No-op.
    }
}
