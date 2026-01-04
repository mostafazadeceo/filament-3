<?php

namespace Vendor\FilamentPayrollAttendanceIr\Infrastructure\Drivers;

use Vendor\FilamentPayrollAttendanceIr\Domain\Models\TimeEvent;

class KioskDriver extends AbstractAttendanceDriver
{
    /**
     * @param  array<string, mixed>  $payload
     */
    public function capture(array $payload): TimeEvent
    {
        return $this->createEvent($payload, 'kiosk');
    }
}
