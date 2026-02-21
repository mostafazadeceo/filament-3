<?php

declare(strict_types=1);

namespace Haida\SmsBulk\Services;

use Haida\SmsBulk\Models\SmsBulkQuietHoursProfile;
use Illuminate\Support\Carbon;

class QuietHoursService
{
    public function isWithinAllowedWindow(?SmsBulkQuietHoursProfile $profile, ?Carbon $at = null): bool
    {
        if (! $profile) {
            return true;
        }

        $at ??= Carbon::now($profile->timezone ?: 'UTC');
        $day = (int) $at->dayOfWeek;

        $allowedDays = $profile->allowed_days ?? [0, 1, 2, 3, 4, 5, 6];
        if (! in_array($day, $allowedDays, true)) {
            return false;
        }

        $time = $at->format('H:i');

        return $time >= $profile->start_time && $time <= $profile->end_time;
    }

    public function nextAllowedAt(?SmsBulkQuietHoursProfile $profile, ?Carbon $from = null): ?Carbon
    {
        if (! $profile) {
            return $from;
        }

        $from ??= Carbon::now($profile->timezone ?: 'UTC');

        if ($this->isWithinAllowedWindow($profile, $from)) {
            return $from;
        }

        $cursor = $from->copy()->startOfMinute();
        for ($i = 0; $i < 7 * 24 * 60; $i++) {
            $cursor->addMinute();
            if ($this->isWithinAllowedWindow($profile, $cursor)) {
                return $cursor;
            }
        }

        return null;
    }
}
