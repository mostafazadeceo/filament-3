<?php

namespace Vendor\FilamentPayrollAttendanceIr\Infrastructure\Drivers;

use Carbon\CarbonInterface;
use Vendor\FilamentPayrollAttendanceIr\Domain\Enums\TimeEventType;
use Vendor\FilamentPayrollAttendanceIr\Domain\Models\TimeEvent;

abstract class AbstractAttendanceDriver implements AttendanceCaptureDriverInterface
{
    /**
     * @param  array<string, mixed>  $payload
     */
    protected function createEvent(array $payload, string $source): TimeEvent
    {
        $payload = app(\Vendor\FilamentPayrollAttendanceIr\Application\Services\PrivacyEnforcer::class)
            ->sanitizeTimeEventPayload($payload);

        $eventAt = $payload['event_at'] ?? now();
        if ($eventAt instanceof CarbonInterface) {
            $eventAt = $eventAt->toDateTimeString();
        }

        $eventType = $payload['event_type'] ?? TimeEventType::ClockIn;
        if (is_string($eventType)) {
            $eventType = TimeEventType::from($eventType);
        }

        return tap(TimeEvent::query()->create([
            'company_id' => $payload['company_id'] ?? null,
            'branch_id' => $payload['branch_id'] ?? null,
            'employee_id' => $payload['employee_id'],
            'event_at' => $eventAt,
            'event_type' => $eventType,
            'source' => $payload['source'] ?? $source,
            'device_ref' => $payload['device_ref'] ?? null,
            'latitude' => $payload['latitude'] ?? null,
            'longitude' => $payload['longitude'] ?? null,
            'wifi_ssid' => $payload['wifi_ssid'] ?? null,
            'ip_address' => $payload['ip_address'] ?? null,
            'proof_type' => $payload['proof_type'] ?? null,
            'proof_payload' => $payload['proof_payload'] ?? null,
            'is_verified' => (bool) ($payload['is_verified'] ?? false),
            'verified_by' => $payload['verified_by'] ?? null,
            'verified_at' => $payload['verified_at'] ?? null,
            'metadata' => $payload['metadata'] ?? null,
        ]), function (TimeEvent $event): void {
            app(\Vendor\FilamentPayrollAttendanceIr\Application\Services\AttendancePolicyEngine::class)
                ->evaluateTimeEvent($event);
        });
    }
}
