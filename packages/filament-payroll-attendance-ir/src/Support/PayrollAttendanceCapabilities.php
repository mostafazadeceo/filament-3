<?php

namespace Vendor\FilamentPayrollAttendanceIr\Support;

use Filamat\IamSuite\Contracts\CapabilityRegistryInterface;
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

final class PayrollAttendanceCapabilities
{
    private static bool $registered = false;

    public static function register(CapabilityRegistryInterface $registry): void
    {
        if (self::$registered) {
            return;
        }

        $registry->register(
            'filament-payroll-attendance-ir',
            self::permissions(),
            [
                'payroll_attendance' => true,
            ],
            [],
            [
                PayrollEmployeePolicy::class,
                PayrollContractPolicy::class,
                PayrollAttendanceShiftPolicy::class,
                PayrollAttendanceSchedulePolicy::class,
                PayrollTimePunchPolicy::class,
                PayrollAttendanceRecordPolicy::class,
                PayrollLeaveTypePolicy::class,
                PayrollLeaveRequestPolicy::class,
                PayrollAttendanceExceptionPolicy::class,
                PayrollAttendancePolicyPolicy::class,
                PayrollTimeEventPolicy::class,
                TimesheetPolicy::class,
                OvertimeRequestPolicy::class,
                MissionRequestPolicy::class,
                WorkCalendarPolicy::class,
                HolidayRulePolicy::class,
                EmployeeConsentPolicy::class,
                SensitiveAccessLogPolicy::class,
                PayrollAiLogPolicy::class,
                PayrollRunPolicy::class,
                PayrollSlipPolicy::class,
                PayrollLoanPolicy::class,
                PayrollAdvancePolicy::class,
                PayrollMinimumWageTablePolicy::class,
                PayrollAllowanceTablePolicy::class,
                PayrollInsuranceTablePolicy::class,
                PayrollTaxTablePolicy::class,
            ],
            [
                'payroll' => 'حقوق و دستمزد',
                'payroll_master' => 'اطلاعات پایه منابع انسانی',
                'payroll_attendance' => 'حضور و غیاب',
                'payroll_salary' => 'حقوق و دستمزد',
                'payroll_settings' => 'تنظیمات حقوق',
                'payroll_report' => 'گزارش‌ها',
                'payroll_exception' => 'استثناها',
                'payroll_ai' => 'هوش مصنوعی',
                'payroll_policy' => 'سیاست‌ها',
                'payroll_calendar' => 'تقویم کاری',
                'payroll_time' => 'رویدادهای زمانی',
                'payroll_request' => 'درخواست‌ها',
                'payroll_privacy' => 'حریم خصوصی',
                'payroll_audit' => 'ممیزی',
            ]
        );

        self::$registered = true;
    }

    /**
     * @return array<int, string>
     */
    public static function permissions(): array
    {
        return [
            'payroll.view',
            'payroll.employee.view',
            'payroll.employee.manage',
            'payroll.contract.view',
            'payroll.contract.manage',
            'payroll.shift.view',
            'payroll.shift.manage',
            'payroll.schedule.view',
            'payroll.schedule.manage',
            'payroll.punch.view',
            'payroll.punch.manage',
            'payroll.time_event.view',
            'payroll.time_event.manage',
            'payroll.attendance.view',
            'payroll.attendance.manage',
            'payroll.attendance.approve',
            'payroll.attendance.lock',
            'payroll.timesheet.view',
            'payroll.timesheet.manage',
            'payroll.timesheet.approve',
            'payroll.exception.view',
            'payroll.exception.manage',
            'payroll.exception.resolve',
            'payroll.policy.view',
            'payroll.policy.manage',
            'payroll.calendar.view',
            'payroll.calendar.manage',
            'payroll.leave.view',
            'payroll.leave.request',
            'payroll.leave.approve',
            'payroll.leave.manage',
            'payroll.mission.view',
            'payroll.mission.request',
            'payroll.mission.approve',
            'payroll.mission.manage',
            'payroll.overtime.view',
            'payroll.overtime.request',
            'payroll.overtime.approve',
            'payroll.overtime.manage',
            'payroll.run.view',
            'payroll.run.manage',
            'payroll.run.approve',
            'payroll.run.post',
            'payroll.run.lock',
            'payroll.slip.view',
            'payroll.loan.manage',
            'payroll.advance.manage',
            'payroll.settings.view',
            'payroll.settings.manage',
            'payroll.report.view',
            'payroll.report.export',
            'payroll.consent.view',
            'payroll.consent.manage',
            'payroll.audit.view',
            'payroll.ai.view',
            'payroll.ai.use',
        ];
    }
}
