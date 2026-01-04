<?php

namespace Vendor\FilamentPayrollAttendanceIr\Domain\Enums;

enum TimesheetPeriodType: string
{
    case Daily = 'daily';
    case Weekly = 'weekly';
    case Monthly = 'monthly';
}
