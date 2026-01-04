<?php

namespace Tests\Feature\AiCore;

use Filamat\IamSuite\Support\TenantContext;
use Haida\FilamentAiCore\Models\AiPolicy;
use Haida\FilamentAiCore\Services\AiCircuitBreaker;
use Haida\FilamentAiCore\Services\AiService;

class AiCircuitBreakerTest extends AiCoreTestCase
{
    public function test_circuit_breaker_disables_provider_after_failures(): void
    {
        config([
            'filament-ai-core.enabled' => true,
            'filament-ai-core.default_provider' => 'n8n',
            'filament-ai-core.providers.n8n.enabled' => true,
            'filament-ai-core.providers.n8n.base_url' => '',
            'filament-ai-core.providers.n8n.secret' => '',
            'filament-ai-core.circuit_breaker.failure_threshold' => 2,
            'filament-ai-core.circuit_breaker.window_seconds' => 300,
            'filament-ai-core.circuit_breaker.cooldown_seconds' => 600,
            'filament-ai-core.circuit_breaker.notify' => false,
        ]);

        $tenant = $this->createTenant('Tenant Circuit');
        TenantContext::setTenant($tenant);

        AiPolicy::query()->create([
            'tenant_id' => $tenant->getKey(),
            'enabled' => true,
            'provider' => 'n8n',
        ]);

        $service = app(AiService::class);

        $service->generate('meetings', 'agenda', 'first prompt');
        $service->generate('meetings', 'agenda', 'second prompt');

        $breaker = app(AiCircuitBreaker::class);
        $this->assertTrue($breaker->isDisabled($tenant->getKey(), 'n8n'));

        $result = $service->generate('meetings', 'agenda', 'third prompt');
        $this->assertTrue($result->ok);
        $this->assertContains('circuit_breaker', $result->warnings);
    }
}
