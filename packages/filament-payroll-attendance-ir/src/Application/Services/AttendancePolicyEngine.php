<?php

namespace Vendor\FilamentPayrollAttendanceIr\Application\Services;

use Vendor\FilamentPayrollAttendanceIr\Application\UseCases\RaiseException;
use Vendor\FilamentPayrollAttendanceIr\Domain\Enums\ExceptionSeverity;
use Vendor\FilamentPayrollAttendanceIr\Domain\Models\AttendancePolicy;
use Vendor\FilamentPayrollAttendanceIr\Domain\Models\EmployeeConsent;
use Vendor\FilamentPayrollAttendanceIr\Domain\Models\TimeEvent;

class AttendancePolicyEngine
{
    public function __construct(
        private readonly AttendancePolicyResolver $resolver,
        private readonly AntiFraudDetector $fraudDetector,
        private readonly RaiseException $raiseException,
    ) {}

    public function evaluateTimeEvent(TimeEvent $event): void
    {
        $policy = $this->resolver->resolve($event->company_id, $event->branch_id);
        if (! $policy) {
            return;
        }

        $rules = $this->mergeRules($policy->rules ?? []);

        $this->checkConsent($event, $policy, $rules);
        $this->checkProofs($event, $rules);
        $this->checkRemoteWork($event, $policy, $rules);

        foreach ($this->fraudDetector->detect($event, $policy) as $violation) {
            $this->raiseException->execute([
                'company_id' => $event->company_id,
                'branch_id' => $event->branch_id,
                'employee_id' => $event->employee_id,
                'time_event_id' => $event->getKey(),
                'type' => $violation['type'],
                'severity' => $violation['severity'] ?? $this->defaultSeverity(),
                'detected_at' => now(),
                'metadata' => $violation['metadata'] ?? null,
                'assigned_to' => $rules['exception_assignee_id'] ?? null,
            ]);
        }
    }

    /**
     * @param  array<string, mixed>  $rules
     */
    private function checkConsent(TimeEvent $event, AttendancePolicy $policy, array $rules): void
    {
        if (! $policy->requires_consent) {
            return;
        }

        $requiresLocation = (bool) ($rules['require_geofence'] ?? false) || (bool) ($rules['require_wifi'] ?? false);
        if ($requiresLocation && ! $this->hasConsent($event->employee_id, 'location_tracking')) {
            $this->raiseException->execute([
                'company_id' => $event->company_id,
                'branch_id' => $event->branch_id,
                'employee_id' => $event->employee_id,
                'time_event_id' => $event->getKey(),
                'type' => 'missing_location_consent',
                'severity' => 'medium',
                'detected_at' => now(),
                'metadata' => ['policy_id' => $policy->getKey()],
                'assigned_to' => $rules['exception_assignee_id'] ?? null,
            ]);
        }
    }

    /**
     * @param  array<string, mixed>  $rules
     */
    private function checkProofs(TimeEvent $event, array $rules): void
    {
        if (($rules['require_geofence'] ?? false) && (! $event->proof_type || $event->proof_type !== 'geofence' || ! $event->is_verified)) {
            $this->raiseException->execute([
                'company_id' => $event->company_id,
                'branch_id' => $event->branch_id,
                'employee_id' => $event->employee_id,
                'time_event_id' => $event->getKey(),
                'type' => 'geofence_required',
                'severity' => 'medium',
                'detected_at' => now(),
            ]);
        }

        if (($rules['require_wifi'] ?? false) && ! $event->wifi_ssid) {
            $this->raiseException->execute([
                'company_id' => $event->company_id,
                'branch_id' => $event->branch_id,
                'employee_id' => $event->employee_id,
                'time_event_id' => $event->getKey(),
                'type' => 'wifi_required',
                'severity' => 'low',
                'detected_at' => now(),
            ]);
        }

        if (($rules['require_device_ref'] ?? false) && ! $event->device_ref) {
            $this->raiseException->execute([
                'company_id' => $event->company_id,
                'branch_id' => $event->branch_id,
                'employee_id' => $event->employee_id,
                'time_event_id' => $event->getKey(),
                'type' => 'device_required',
                'severity' => 'low',
                'detected_at' => now(),
            ]);
        }
    }

    /**
     * @param  array<string, mixed>  $rules
     */
    private function checkRemoteWork(TimeEvent $event, AttendancePolicy $policy, array $rules): void
    {
        if ($policy->allow_remote_work) {
            return;
        }

        if (($rules['remote_only_if_branch'] ?? false) && ! $event->branch_id) {
            $this->raiseException->execute([
                'company_id' => $event->company_id,
                'branch_id' => $event->branch_id,
                'employee_id' => $event->employee_id,
                'time_event_id' => $event->getKey(),
                'type' => 'remote_not_allowed',
                'severity' => 'low',
                'detected_at' => now(),
            ]);
        }
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

    private function hasConsent(int $employeeId, string $type): bool
    {
        return EmployeeConsent::query()
            ->where('employee_id', $employeeId)
            ->where('consent_type', $type)
            ->where('is_granted', true)
            ->exists();
    }

    private function defaultSeverity(): string
    {
        return (string) config('filament-payroll-attendance-ir.exceptions.default_severity', ExceptionSeverity::Low->value);
    }
}
