<?php

namespace Vendor\FilamentPayrollAttendanceIr\Domain\Enums;

enum TimeEventType: string
{
    case ClockIn = 'clock_in';
    case ClockOut = 'clock_out';
    case BreakStart = 'break_start';
    case BreakEnd = 'break_end';
}
