<?php

namespace Haida\FilamentCommerceCore\Services;

use Filamat\IamSuite\Support\TenantContext;
use Haida\FilamentCommerceCore\Models\CommerceException;
use Haida\FilamentCommerceCore\Models\CommerceFraudRule;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Validation\ValidationException;

class CommerceComplianceService
{
    /**
     * @param  array<string, mixed>  $payload
     */
    public function openException(array $payload, ?Authenticatable $actor = null): CommerceException
    {
        $tenantId = $payload['tenant_id'] ?? TenantContext::getTenantId();
        if (! $tenantId) {
            throw ValidationException::withMessages(['tenant_id' => __('filament-commerce-core::messages.compliance.tenant_required')]);
        }

        $idempotencyKey = Arr::get($payload, 'metadata.idempotency_key');
        $ruleKey = Arr::get($payload, 'metadata.rule_key');

        $query = CommerceException::query()
            ->where('tenant_id', $tenantId)
            ->where('type', $payload['type'] ?? 'unknown');

        if (! empty($payload['entity_type']) && ! empty($payload['entity_id'])) {
            $query->where('entity_type', $payload['entity_type'])
                ->where('entity_id', $payload['entity_id']);
        }

        if ($idempotencyKey) {
            $query->where('metadata->idempotency_key', $idempotencyKey);
        }

        if ($ruleKey) {
            $query->where('metadata->rule_key', $ruleKey);
        }

        $existing = $query->first();
        if ($existing) {
            return $existing;
        }

        $exception = CommerceException::query()->create([
            'tenant_id' => $tenantId,
            'type' => $payload['type'] ?? 'unknown',
            'severity' => $payload['severity'] ?? 'medium',
            'status' => $payload['status'] ?? 'open',
            'title' => $payload['title'] ?? __('filament-commerce-core::messages.compliance.default_title'),
            'description' => $payload['description'] ?? null,
            'entity_type' => $payload['entity_type'] ?? null,
            'entity_id' => $payload['entity_id'] ?? null,
            'created_by_user_id' => $payload['created_by_user_id'] ?? $actor?->getAuthIdentifier(),
            'assigned_to_user_id' => $payload['assigned_to_user_id'] ?? null,
            'metadata' => $payload['metadata'] ?? null,
        ]);

        $this->dispatchExceptionNotification($exception);

        return $exception->refresh();
    }

    public function resolveException(CommerceException $exception, ?string $note = null, ?Authenticatable $actor = null, string $status = 'resolved'): CommerceException
    {
        $exception->update([
            'status' => $status,
            'resolved_at' => now(),
            'resolved_by_user_id' => $actor?->getAuthIdentifier() ?? auth()->id(),
            'resolution_note' => $note,
        ]);

        return $exception->refresh();
    }

    /**
     * @param  array<string, mixed>  $metrics
     * @return array<int, CommerceException>
     */
    public function evaluate(string $eventType, array $metrics = [], ?Model $entity = null, ?Authenticatable $actor = null): array
    {
        $tenantId = $metrics['tenant_id']
            ?? ($entity?->getAttribute('tenant_id'))
            ?? TenantContext::getTenantId();

        if (! $tenantId) {
            throw ValidationException::withMessages(['tenant_id' => __('filament-commerce-core::messages.compliance.tenant_required')]);
        }

        $rules = CommerceFraudRule::query()
            ->where('tenant_id', $tenantId)
            ->where('status', 'active')
            ->get();

        $defaults = (array) config('filament-commerce-core.compliance.default_rules', []);

        $exceptions = [];

        foreach ($rules as $rule) {
            $ruleData = [
                'key' => $rule->key,
                'name' => $rule->name,
                'thresholds' => $rule->thresholds ?? [],
                'metadata' => $rule->metadata ?? [],
            ];

            if (! $this->ruleMatchesEvent($ruleData, $eventType)) {
                continue;
            }

            if (! $this->thresholdsMet((array) $ruleData['thresholds'], $metrics, (int) $tenantId, $eventType)) {
                continue;
            }

            $exceptions[] = $this->openException($this->buildExceptionPayload(
                $eventType,
                $metrics,
                $ruleData,
                $entity,
                $actor,
                $tenantId
            ), $actor);
        }

        foreach ($defaults as $defaultRule) {
            if (! is_array($defaultRule)) {
                continue;
            }

            if (! $this->ruleMatchesEvent($defaultRule, $eventType)) {
                continue;
            }

            if (! $this->thresholdsMet((array) ($defaultRule['thresholds'] ?? []), $metrics, (int) $tenantId, $eventType)) {
                continue;
            }

            $exceptions[] = $this->openException($this->buildExceptionPayload(
                $eventType,
                $metrics,
                $defaultRule,
                $entity,
                $actor,
                $tenantId
            ), $actor);
        }

        return $exceptions;
    }

    /**
     * @param  array<string, mixed>  $rule
     */
    protected function ruleMatchesEvent(array $rule, string $eventType): bool
    {
        $event = (string) (Arr::get($rule, 'metadata.event') ?? Arr::get($rule, 'thresholds.event') ?? '');
        if ($event !== '' && $event !== $eventType) {
            return false;
        }

        $key = (string) ($rule['key'] ?? '');
        if ($key !== '' && ($key === $eventType || str_starts_with($key, $eventType.'.'))) {
            return true;
        }

        return $event !== '' || $key === '';
    }

    /**
     * @param  array<string, mixed>  $thresholds
     * @param  array<string, mixed>  $metrics
     */
    protected function thresholdsMet(array $thresholds, array $metrics, int $tenantId, string $eventType): bool
    {
        foreach ($thresholds as $key => $value) {
            if (in_array($key, ['event', 'severity', 'title', 'description'], true)) {
                continue;
            }

            if ($key === 'count_gte') {
                $count = $metrics['count'] ?? null;
                $window = (int) ($thresholds['window_minutes'] ?? 0);
                if ($count === null && $window > 0) {
                    $count = CommerceException::query()
                        ->where('tenant_id', $tenantId)
                        ->where('type', $eventType)
                        ->where('created_at', '>=', now()->subMinutes($window))
                        ->count();
                }

                if ($count !== null && ((int) $count + 1) < (int) $value) {
                    return false;
                }

                continue;
            }

            if (str_ends_with($key, '_gte')) {
                $metricKey = substr((string) $key, 0, -4);
                $metricValue = Arr::get($metrics, $metricKey);
                if ($metricValue !== null && (float) $metricValue < (float) $value) {
                    return false;
                }

                continue;
            }

            if (str_ends_with($key, '_lte')) {
                $metricKey = substr((string) $key, 0, -4);
                $metricValue = Arr::get($metrics, $metricKey);
                if ($metricValue !== null && (float) $metricValue > (float) $value) {
                    return false;
                }

                continue;
            }
        }

        return true;
    }

    /**
     * @param  array<string, mixed>  $metrics
     * @param  array<string, mixed>  $rule
     * @return array<string, mixed>
     */
    protected function buildExceptionPayload(
        string $eventType,
        array $metrics,
        array $rule,
        ?Model $entity,
        ?Authenticatable $actor,
        int $tenantId
    ): array {
        $thresholds = (array) ($rule['thresholds'] ?? []);
        $metadata = (array) ($rule['metadata'] ?? []);

        $severity = $thresholds['severity'] ?? $metadata['severity'] ?? 'medium';
        $title = $thresholds['title'] ?? $metadata['title'] ?? ($rule['name'] ?? __('filament-commerce-core::messages.compliance.default_title'));
        $description = $thresholds['description'] ?? $metadata['description'] ?? null;

        return [
            'tenant_id' => $tenantId,
            'type' => $eventType,
            'severity' => $severity,
            'title' => $title,
            'description' => $description,
            'entity_type' => $entity ? $entity::class : null,
            'entity_id' => $entity?->getKey(),
            'created_by_user_id' => $actor?->getAuthIdentifier(),
            'metadata' => [
                'idempotency_key' => $metrics['idempotency_key'] ?? null,
                'rule_key' => $rule['key'] ?? null,
                'rule_name' => $rule['name'] ?? null,
                'metrics' => $metrics,
                'thresholds' => $thresholds,
            ],
        ];
    }

    protected function dispatchExceptionNotification(CommerceException $exception): void
    {
        $panelId = (string) config('filament-commerce-core.compliance.notifications.panel', 'tenant');
        $event = (string) config('filament-commerce-core.compliance.notifications.exception_event', 'commerce.compliance.exception.created');

        if ($panelId === '' || $event === '' || ! class_exists(\Haida\FilamentNotify\Core\Support\Triggers\TriggerDispatcher::class)) {
            return;
        }

        try {
            app(\Haida\FilamentNotify\Core\Support\Triggers\TriggerDispatcher::class)
                ->dispatchForEloquent($panelId, $exception, $event);
        } catch (\Throwable) {
            // Keep compliance workflow resilient if notifications fail.
        }
    }
}
