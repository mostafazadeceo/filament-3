<?php

namespace Vendor\FilamentPayrollAttendanceIr\Domain\Enums;

enum ApprovalStatus: string
{
    case Draft = 'draft';
    case Approved = 'approved';
    case Posted = 'posted';
    case Locked = 'locked';
}
