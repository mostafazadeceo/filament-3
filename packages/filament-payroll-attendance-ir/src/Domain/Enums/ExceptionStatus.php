<?php

namespace Vendor\FilamentPayrollAttendanceIr\Domain\Enums;

enum ExceptionStatus: string
{
    case Open = 'open';
    case InReview = 'in_review';
    case Resolved = 'resolved';
    case Dismissed = 'dismissed';
}
