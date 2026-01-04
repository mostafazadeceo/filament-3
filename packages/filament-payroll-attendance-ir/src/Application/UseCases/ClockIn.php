<?php

namespace Vendor\FilamentPayrollAttendanceIr\Application\UseCases;

use Carbon\CarbonInterface;
use Vendor\FilamentPayrollAttendanceIr\Domain\Enums\TimeEventType;
use Vendor\FilamentPayrollAttendanceIr\Domain\Models\TimeEvent;
use Vendor\FilamentPayrollAttendanceIr\Models\PayrollEmployee;

class ClockIn
{
    /**
     * @param  array<string, mixed>  $payload
     */
    public function execute(int $employeeId, array $payload = []): TimeEvent
    {
        $employee = PayrollEmployee::query()->findOrFail($employeeId);

        $payload = app(\Vendor\FilamentPayrollAttendanceIr\Application\Services\PrivacyEnforcer::class)
            ->sanitizeTimeEventPayload(array_merge($payload, ['employee_id' => $employeeId]));

        $eventAt = $payload['event_at'] ?? now();
        if ($eventAt instanceof CarbonInterface) {
            $eventAt = $eventAt->toDateTimeString();
        }

        return tap(TimeEvent::query()->create([
            'company_id' => $payload['company_id'] ?? $employee->company_id,
            'branch_id' => $payload['branch_id'] ?? $employee->branch_id,
            'employee_id' => $employee->getKey(),
            'event_at' => $eventAt,
            'event_type' => TimeEventType::ClockIn,
            'source' => $payload['source'] ?? 'manual',
            'device_ref' => $payload['device_ref'] ?? null,
            'latitude' => $payload['latitude'] ?? null,
            'longitude' => $payload['longitude'] ?? null,
            'wifi_ssid' => $payload['wifi_ssid'] ?? null,
            'ip_address' => $payload['ip_address'] ?? null,
            'proof_type' => $payload['proof_type'] ?? null,
            'proof_payload' => $payload['proof_payload'] ?? null,
            'metadata' => $payload['metadata'] ?? null,
        ]), function (TimeEvent $event): void {
            app(\Vendor\FilamentPayrollAttendanceIr\Application\Services\AttendancePolicyEngine::class)
                ->evaluateTimeEvent($event);
        });
    }
}
