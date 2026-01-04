<?php

namespace Vendor\FilamentPayrollAttendanceIr\Domain\Enums;

enum ExceptionSeverity: string
{
    case Low = 'low';
    case Medium = 'medium';
    case High = 'high';
}
