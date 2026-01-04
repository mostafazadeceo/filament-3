<?php

namespace Vendor\FilamentPayrollAttendanceIr\Infrastructure\Drivers;

use Vendor\FilamentPayrollAttendanceIr\Domain\Models\TimeEvent;

interface AttendanceCaptureDriverInterface
{
    /**
     * @param  array<string, mixed>  $payload
     */
    public function capture(array $payload): TimeEvent;
}
