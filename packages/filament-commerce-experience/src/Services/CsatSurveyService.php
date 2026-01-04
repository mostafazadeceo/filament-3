<?php

namespace Haida\FilamentCommerceExperience\Services;

use Filamat\IamSuite\Support\TenantContext;
use Haida\FilamentCommerceExperience\Models\ExperienceCsatSurvey;
use Haida\FilamentNotify\Core\Support\Triggers\TriggerDispatcher;
use Illuminate\Database\DatabaseManager;
use Illuminate\Validation\ValidationException;

class CsatSurveyService
{
    public function __construct(protected DatabaseManager $db)
    {
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    public function createSurvey(array $payload = []): ExperienceCsatSurvey
    {
        $tenantId = $payload['tenant_id'] ?? TenantContext::getTenantId();
        if (! $tenantId) {
            throw ValidationException::withMessages(['tenant_id' => 'شناسه تننت الزامی است.']);
        }

        return $this->db->transaction(function () use ($payload, $tenantId): ExperienceCsatSurvey {
            $survey = ExperienceCsatSurvey::query()->create([
                'tenant_id' => $tenantId,
                'order_id' => $payload['order_id'] ?? null,
                'customer_id' => $payload['customer_id'] ?? null,
                'channel' => $payload['channel'] ?? null,
                'status' => $payload['status'] ?? 'sent',
                'sent_at' => $payload['sent_at'] ?? now(),
                'metadata' => $payload['metadata'] ?? null,
            ]);

            $this->dispatchNotification($survey);

            return $survey;
        });
    }

    protected function dispatchNotification(ExperienceCsatSurvey $survey): void
    {
        $panelId = (string) config('filament-commerce-experience.notifications.panel', 'tenant');
        $event = (string) config('filament-commerce-experience.notifications.csat_event', 'experience.csat.sent');

        if ($panelId === '' || $event === '' || ! class_exists(TriggerDispatcher::class)) {
            return;
        }

        try {
            app(TriggerDispatcher::class)->dispatchForEloquent($panelId, $survey, $event);
        } catch (\Throwable) {
            // Keep CSAT flow resilient if notifications fail.
        }
    }
}
