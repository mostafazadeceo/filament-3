<?php

namespace Vendor\FilamentPayrollAttendanceIr\Application\Services;

use Vendor\FilamentPayrollAttendanceIr\Domain\Enums\TimeEventType;
use Vendor\FilamentPayrollAttendanceIr\Domain\Models\EmployeeConsent;

class PrivacyEnforcer
{
    /**
     * @param  array<string, mixed>  $payload
     * @return array<string, mixed>
     */
    public function sanitizeTimeEventPayload(array $payload): array
    {
        $eventType = $payload['event_type'] ?? TimeEventType::ClockIn;
        if (is_string($eventType)) {
            $eventType = TimeEventType::from($eventType);
        }

        $locationFields = [
            'latitude',
            'longitude',
            'wifi_ssid',
            'ip_address',
            'proof_type',
            'proof_payload',
        ];

        $locationPresent = false;
        foreach ($locationFields as $field) {
            if (array_key_exists($field, $payload) && $payload[$field] !== null) {
                $locationPresent = true;
                break;
            }
        }

        $locationEnabled = (bool) config('filament-payroll-attendance-ir.privacy.location_tracking_enabled', true);
        $locationWindowed = in_array($eventType, [TimeEventType::ClockIn, TimeEventType::ClockOut], true);
        $locationConsent = $this->hasConsent($payload['employee_id'] ?? null, 'location_tracking');

        if ($locationPresent && (! $locationEnabled || ! $locationWindowed || ! $locationConsent)) {
            foreach ($locationFields as $field) {
                unset($payload[$field]);
            }

            $payload['metadata']['privacy'] = array_merge(
                (array) ($payload['metadata']['privacy'] ?? []),
                [
                    'location_redacted' => true,
                    'location_enabled' => $locationEnabled,
                    'location_windowed' => $locationWindowed,
                    'location_consent' => $locationConsent,
                ]
            );
        }

        $biometricEnabled = (bool) config('filament-payroll-attendance-ir.privacy.biometric_enabled', false);
        $biometricConsent = $this->hasConsent($payload['employee_id'] ?? null, 'biometric_verification');
        $proofType = $payload['proof_type'] ?? null;

        if ($proofType === 'biometric' && (! $biometricEnabled || ! $biometricConsent)) {
            unset($payload['proof_type'], $payload['proof_payload']);
            $payload['metadata']['privacy'] = array_merge(
                (array) ($payload['metadata']['privacy'] ?? []),
                [
                    'biometric_redacted' => true,
                    'biometric_enabled' => $biometricEnabled,
                    'biometric_consent' => $biometricConsent,
                ]
            );
        }

        return $payload;
    }

    private function hasConsent(?int $employeeId, string $type): bool
    {
        if (! $employeeId) {
            return false;
        }

        return EmployeeConsent::query()
            ->where('employee_id', $employeeId)
            ->where('consent_type', $type)
            ->where('is_granted', true)
            ->exists();
    }
}
