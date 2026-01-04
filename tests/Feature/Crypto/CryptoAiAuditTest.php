<?php

declare(strict_types=1);

namespace Tests\Feature\Crypto;

use Filamat\IamSuite\Models\Tenant;
use Filamat\IamSuite\Models\Subscription;
use Filamat\IamSuite\Models\SubscriptionPlan;
use Haida\FilamentCryptoGateway\Contracts\AiInsightProvider;
use Haida\FilamentCryptoGateway\Models\CryptoAiReport;
use Haida\FilamentCryptoGateway\Models\CryptoInvoice;
use Haida\FilamentCryptoGateway\Services\AiReportService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CryptoAiAuditTest extends TestCase
{
    use RefreshDatabase;

    public function test_ai_audit_report_uses_provider_when_enabled(): void
    {
        config(['filament-crypto-gateway.ai.enabled' => true]);
        config(['filament-crypto-core.plans.free.features.crypto.ai_auditor' => true]);

        app()->bind(AiInsightProvider::class, fn () => new class implements AiInsightProvider {
            public function generateInsights(array $context): array
            {
                return ['summary' => 'AI summary'];
            }
        });

        $tenant = Tenant::query()->create([
            'name' => 'Tenant Crypto AI',
            'slug' => 'tenant-crypto-ai',
            'status' => 'active',
        ]);

        $plan = SubscriptionPlan::query()->create([
            'tenant_id' => $tenant->getKey(),
            'code' => 'crypto-pro',
            'scope' => 'tenant',
            'name' => 'Crypto Pro',
            'price' => 0,
            'currency' => 'irr',
            'period_days' => 30,
            'trial_days' => 0,
            'features' => [
                'crypto_features' => [
                    'crypto.ai_auditor' => true,
                ],
            ],
            'is_active' => true,
        ]);

        Subscription::query()->create([
            'tenant_id' => $tenant->getKey(),
            'plan_id' => $plan->getKey(),
            'status' => 'active',
            'provider' => 'test',
            'provider_ref' => 'test',
        ]);

        CryptoInvoice::query()->create([
            'tenant_id' => $tenant->getKey(),
            'provider' => 'cryptomus',
            'order_id' => 'ORDER-AI',
            'external_uuid' => 'inv-ai',
            'amount' => 5,
            'currency' => 'USDT',
            'status' => 'paid',
        ]);

        $report = app(AiReportService::class)->generate($tenant->getKey(), null, null, null, 'daily');

        $this->assertSame('daily', $report->period);
        $this->assertNotNull($report->meta);
        $this->assertSame(1, CryptoAiReport::query()->count());
        $this->assertStringContainsString('AI summary', $report->summary_md ?? '');
    }
}
