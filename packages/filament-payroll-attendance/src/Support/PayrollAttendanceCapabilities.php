<?php

namespace Haida\FilamentPayrollAttendance\Support;

use Filamat\IamSuite\Contracts\CapabilityRegistryInterface;

final class PayrollAttendanceCapabilities
{
    private static bool $registered = false;

    public static function register(CapabilityRegistryInterface $registry): void
    {
        if (self::$registered) {
            return;
        }

        $registry->register(
            'filament-payroll-attendance',
            self::permissions(),
            [
                'payroll_attendance' => true,
            ],
            [],
            [],
            [
                'payroll' => 'حقوق و دستمزد',
                'payroll_master' => 'اطلاعات پایه',
                'payroll_attendance' => 'حضور و غیاب',
                'payroll_run' => 'پردازش حقوق',
                'payroll_finance' => 'وام و مساعده',
                'payroll_report' => 'گزارش‌ها',
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
            'payroll.attendance.view',
            'payroll.attendance.manage',
            'payroll.attendance.approve',
            'payroll.leave.view',
            'payroll.leave.manage',
            'payroll.leave.approve',
            'payroll.run.view',
            'payroll.run.manage',
            'payroll.run.approve',
            'payroll.run.post',
            'payroll.run.lock',
            'payroll.slip.view',
            'payroll.table.view',
            'payroll.table.manage',
            'payroll.loan.view',
            'payroll.loan.manage',
            'payroll.advance.view',
            'payroll.advance.manage',
            'payroll.settlement.view',
            'payroll.settlement.manage',
            'payroll.report.view',
            'payroll.report.export',
        ];
    }
}
