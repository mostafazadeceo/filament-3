<?php

namespace Vendor\FilamentPayrollAttendanceIr\Domain\States;

use Vendor\FilamentPayrollAttendanceIr\Domain\Enums\ApprovalStatus;

final class ApprovalStateMachine
{
    /**
     * @var array<string, array<int, ApprovalStatus>>
     */
    private static array $transitions = [
        'draft' => [
            ApprovalStatus::Approved,
        ],
        'approved' => [
            ApprovalStatus::Posted,
        ],
        'posted' => [
            ApprovalStatus::Locked,
        ],
        'locked' => [],
    ];

    public static function canTransition(ApprovalStatus $from, ApprovalStatus $to): bool
    {
        return in_array($to, self::$transitions[$from->value] ?? [], true);
    }
}
