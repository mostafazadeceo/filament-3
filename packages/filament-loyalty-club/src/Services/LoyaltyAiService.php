<?php

namespace Haida\FilamentLoyaltyClub\Services;

use Haida\FilamentLoyaltyClub\Contracts\AiProviderInterface;
use Haida\FilamentLoyaltyClub\Models\LoyaltyAuditEvent;
use RuntimeException;

class LoyaltyAiService
{
    public function __construct(protected AiProviderInterface $provider) {}

    /**
     * @param  array<string, mixed>  $segmentContext
     * @return array<int, array<string, mixed>>
     */
    public function recommendOffers(array $segmentContext): array
    {
        $this->assertEnabled();
        $result = $this->provider->recommendOffers($segmentContext);
        $this->audit('ai.recommend_offers', $segmentContext);

        return $result;
    }

    /**
     * @param  array<string, mixed>  $customerContext
     * @return array<string, mixed>
     */
    public function detectChurnRisk(array $customerContext): array
    {
        $this->assertEnabled();
        $result = $this->provider->detectChurnRisk($customerContext);
        $this->audit('ai.detect_churn', $customerContext);

        return $result;
    }

    /**
     * @param  array<string, mixed>  $campaignContext
     * @return array<int, array<string, mixed>>
     */
    public function draftCampaignCopy(array $campaignContext): array
    {
        $this->assertEnabled();
        $result = $this->provider->draftCampaignCopy($campaignContext);
        $this->audit('ai.draft_copy', $campaignContext);

        return $result;
    }

    /**
     * @param  array<string, mixed>  $signalContext
     */
    public function explainFraudSignal(array $signalContext): string
    {
        $this->assertEnabled();
        $result = $this->provider->explainFraudSignal($signalContext);
        $this->audit('ai.explain_fraud', $signalContext);

        return $result;
    }

    protected function assertEnabled(): void
    {
        if (! (bool) config('filament-loyalty-club.features.ai.enabled', false)) {
            throw new RuntimeException('هوش مصنوعی غیرفعال است.');
        }
    }

    protected function audit(string $action, array $context): void
    {
        $meta = $this->redact($context);
        if (! isset($meta['tenant_id'])) {
            return;
        }

        LoyaltyAuditEvent::query()->create([
            'tenant_id' => $meta['tenant_id'] ?? null,
            'actor_id' => $meta['actor_id'] ?? null,
            'actor_type' => $meta['actor_type'] ?? null,
            'action' => $action,
            'meta' => $meta,
            'occurred_at' => now(),
        ]);
    }

    protected function redact(array $context): array
    {
        if (! (bool) config('filament-loyalty-club.privacy.redact_logs', true)) {
            return $context;
        }

        $sensitive = ['phone', 'email', 'ip', 'device_id'];
        foreach ($sensitive as $key) {
            if (array_key_exists($key, $context)) {
                $context[$key] = 'redacted';
            }
        }

        return $context;
    }
}
