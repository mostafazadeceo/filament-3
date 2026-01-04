<?php

namespace Vendor\FilamentPayrollAttendanceIr\Domain\States;

use Vendor\FilamentPayrollAttendanceIr\Domain\Enums\RequestStatus;

final class RequestStateMachine
{
    /**
     * @var array<string, array<int, RequestStatus>>
     */
    private static array $transitions = [
        'pending' => [
            RequestStatus::Approved,
            RequestStatus::Rejected,
            RequestStatus::Cancelled,
        ],
        'approved' => [
            RequestStatus::Cancelled,
        ],
        'rejected' => [],
        'cancelled' => [],
    ];

    public static function canTransition(RequestStatus $from, RequestStatus $to): bool
    {
        return in_array($to, self::$transitions[$from->value] ?? [], true);
    }
}
