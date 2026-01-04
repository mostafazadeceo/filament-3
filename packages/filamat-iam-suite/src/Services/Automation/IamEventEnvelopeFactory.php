<?php

declare(strict_types=1);

namespace Filamat\IamSuite\Services\Automation;

use Filamat\IamSuite\Contracts\IamEvent;
use Filamat\IamSuite\Models\Webhook;
use Illuminate\Support\Str;

class IamEventEnvelopeFactory
{
    /**
     * @return array<string, mixed>
     */
    public function build(IamEvent $event, ?Webhook $webhook = null): array
    {
        $catalog = (string) config('filamat-iam.automation.event_catalog', 'n8n_event_catalog');
        $payload = $event->payload();
        $occurredAt = $payload['occurred_at'] ?? now()->toIso8601String();
        unset($payload['occurred_at']);

        $envelope = [
            'event' => $event->eventName(),
            'version' => (string) config($catalog.'.envelope.defaults.version', '1.0'),
            'tenant_id' => $event->tenantId(),
            'occurred_at' => $occurredAt,
            'idempotency_key' => (string) Str::uuid(),
        ];

        foreach (['actor', 'subject', 'context', 'data', 'links'] as $key) {
            if (array_key_exists($key, $payload) && $payload[$key] !== null) {
                $envelope[$key] = $payload[$key];
            }
        }

        $defaults = (array) config('filamat-iam.automation.redaction_defaults', []);
        $overrides = is_array($webhook?->redaction_policy ?? null) ? $webhook->redaction_policy : [];
        $policy = array_replace_recursive($defaults, $overrides);

        return $this->applyRedaction($envelope, $policy);
    }

    /**
     * @param  array<string, mixed>  $envelope
     * @param  array<string, mixed>  $policy
     * @return array<string, mixed>
     */
    protected function applyRedaction(array $envelope, array $policy): array
    {
        if (! isset($envelope['actor']) || ! is_array($envelope['actor'])) {
            return $envelope;
        }

        $actorPolicy = is_array($policy['actor'] ?? null) ? $policy['actor'] : [];

        if (($actorPolicy['ip'] ?? null) === 'remove') {
            unset($envelope['actor']['ip']);
        }

        if (($actorPolicy['ua'] ?? null) === 'remove') {
            unset($envelope['actor']['ua']);
        }

        if (isset($envelope['actor']['email'])) {
            $emailPolicy = $actorPolicy['email'] ?? null;
            if ($emailPolicy === 'remove') {
                unset($envelope['actor']['email']);
            } elseif ($emailPolicy === 'mask') {
                $envelope['actor']['email'] = $this->maskEmail((string) $envelope['actor']['email']);
            }
        }

        if (isset($envelope['actor']['email_masked']) && ($actorPolicy['email'] ?? null) === 'remove') {
            unset($envelope['actor']['email_masked']);
        }

        return $envelope;
    }

    protected function maskEmail(string $email): string
    {
        if (! str_contains($email, '@')) {
            return '***';
        }

        [$name, $domain] = explode('@', $email, 2);
        if ($name === '') {
            return '***@'.$domain;
        }

        return substr($name, 0, 1).'***@'.$domain;
    }
}
