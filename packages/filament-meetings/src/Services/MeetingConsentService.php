<?php

namespace Haida\FilamentMeetings\Services;

use Filamat\IamSuite\Services\AuditService;
use Haida\FilamentAiCore\Models\AiRequest;
use Haida\FilamentAiCore\Services\AiPolicyService;
use Haida\FilamentMeetings\DTOs\MeetingDto;
use Haida\FilamentMeetings\Events\MeetingConsentConfirmed;
use Haida\FilamentMeetings\Models\Meeting;
use Haida\FilamentMeetings\Models\MeetingAttendee;
use Illuminate\Contracts\Auth\Authenticatable;

class MeetingConsentService
{
    public function __construct(
        protected AiPolicyService $policyService,
        protected AuditService $auditService,
    ) {}

    public function isConsentRequired(Meeting $meeting): bool
    {
        $policy = $this->policyService->resolvePolicy($meeting->tenant);

        return (bool) ($meeting->consent_required && ($policy['consent_required_meetings'] ?? true));
    }

    public function isConsentSatisfied(Meeting $meeting, ?Authenticatable $actor = null): bool
    {
        if (! $this->isConsentRequired($meeting)) {
            return true;
        }

        if ($meeting->consent_mode === 'manual') {
            return (bool) $meeting->consent_confirmed_at;
        }

        $attendees = $meeting->attendees;
        if ($attendees->isEmpty()) {
            return false;
        }

        return $attendees->every(function (MeetingAttendee $attendee) {
            if ($attendee->attendance_status === 'declined') {
                return true;
            }

            return (bool) $attendee->consent_granted_at;
        });
    }

    /**
     * @return array{ok: bool, message?: string}
     */
    public function confirmConsent(Meeting $meeting, ?Authenticatable $actor = null): array
    {
        $actorId = $actor?->getAuthIdentifier();

        if (! $this->isConsentRequired($meeting)) {
            return ['ok' => true];
        }

        if ($meeting->consent_mode === 'manual') {
            $meeting->forceFill([
                'consent_confirmed_at' => now(),
                'consent_confirmed_by' => $actorId,
            ])->save();
        } else {
            if (! $actorId) {
                return ['ok' => false, 'message' => 'کاربر معتبر نیست.'];
            }

            $attendee = $meeting->attendees()->where('user_id', $actorId)->first();
            if (! $attendee) {
                return ['ok' => false, 'message' => 'شرکت‌کننده برای ثبت رضایت یافت نشد.'];
            }

            $attendee->forceFill([
                'consent_granted_at' => now(),
            ])->save();
        }

        $this->auditService->log('meetings.consent.confirmed', $meeting, [
            'mode' => $meeting->consent_mode,
            'actor_id' => $actorId,
        ], $actor);

        $this->logConsentRequest($meeting, $actorId);

        event(new MeetingConsentConfirmed(MeetingDto::fromModel($meeting), $actorId, $meeting->consent_mode));

        return ['ok' => true];
    }

    protected function logConsentRequest(Meeting $meeting, ?int $actorId): void
    {
        AiRequest::query()->create([
            'tenant_id' => $meeting->tenant_id,
            'actor_id' => $actorId,
            'module' => 'meetings',
            'action_type' => 'consent.confirmed',
            'input_hash' => hash('sha256', 'meeting:'.$meeting->getKey().':'.$actorId),
            'output_hash' => null,
            'status' => 'ok',
            'latency_ms' => null,
            'created_at' => now(),
        ]);
    }
}
