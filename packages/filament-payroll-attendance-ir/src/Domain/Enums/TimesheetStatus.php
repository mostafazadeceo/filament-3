<?php

namespace Vendor\FilamentPayrollAttendanceIr\Domain\Enums;

enum TimesheetStatus: string
{
    case Draft = 'draft';
    case Approved = 'approved';
    case Locked = 'locked';
}
