<?php

namespace Vendor\FilamentPayrollAttendanceIr\Application\Services;

use Carbon\Carbon;
use Vendor\FilamentPayrollAttendanceIr\Domain\Models\AttendancePolicy;
use Vendor\FilamentPayrollAttendanceIr\Domain\Models\TimeEvent;

class AntiFraudDetector
{
    /**
     * @return array<int, array<string, mixed>>
     */
    public function detect(TimeEvent $event, ?AttendancePolicy $policy = null): array
    {
        $violations = [];
        $rules = $this->mergeRules($policy?->rules ?? []);

        $previous = TimeEvent::query()
            ->where('employee_id', $event->employee_id)
            ->where('event_at', '<', $event->event_at)
            ->orderByDesc('event_at')
            ->first();

        if ($previous) {
            $minutesDiff = Carbon::parse($previous->event_at)->diffInMinutes($event->event_at);
            $minInterval = (int) ($rules['min_event_interval_minutes']
                ?? config('filament-payroll-attendance-ir.fraud.min_event_interval_minutes', 2));

            if ($previous->event_type === $event->event_type && $minutesDiff <= $minInterval) {
                $violations[] = [
                    'type' => 'duplicate_event',
                    'severity' => 'low',
                    'metadata' => [
                        'previous_event_id' => $previous->getKey(),
                        'minutes_diff' => $minutesDiff,
                    ],
                ];
            }

            if ($this->hasLocation($previous) && $this->hasLocation($event)) {
                $speed = $this->calculateSpeedKmh($previous, $event);
                $maxSpeed = (float) ($rules['max_travel_speed_kmh']
                    ?? config('filament-payroll-attendance-ir.fraud.max_travel_speed_kmh', 120));

                if ($speed > $maxSpeed) {
                    $violations[] = [
                        'type' => 'impossible_travel',
                        'severity' => 'medium',
                        'metadata' => [
                            'previous_event_id' => $previous->getKey(),
                            'speed_kmh' => round($speed, 2),
                        ],
                    ];
                }
            }
        }

        if (($rules['manual_edit_requires_reason'] ?? false) && $event->source === 'manual') {
            $reason = (string) ($event->metadata['reason'] ?? '');
            if ($reason === '') {
                $violations[] = [
                    'type' => 'manual_edit_without_reason',
                    'severity' => 'low',
                ];
            }
        }

        return $violations;
    }

    /**
     * @param  array<string, mixed>  $rules
     * @return array<string, mixed>
     */
    private function mergeRules(array $rules): array
    {
        return array_merge(
            (array) config('filament-payroll-attendance-ir.policy.default_rules', []),
            $rules
        );
    }

    private function hasLocation(TimeEvent $event): bool
    {
        return $event->latitude !== null && $event->longitude !== null;
    }

    private function calculateSpeedKmh(TimeEvent $from, TimeEvent $to): float
    {
        $distanceKm = $this->haversine(
            (float) $from->latitude,
            (float) $from->longitude,
            (float) $to->latitude,
            (float) $to->longitude
        );

        $hours = max(0.0001, Carbon::parse($from->event_at)->diffInSeconds($to->event_at) / 3600);

        return $distanceKm / $hours;
    }

    private function haversine(float $lat1, float $lon1, float $lat2, float $lon2): float
    {
        $earthRadius = 6371;
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);
        $a = sin($dLat / 2) ** 2
            + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLon / 2) ** 2;
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }
}
