<?php

namespace Tests\Feature\FeatureGates;

use Filamat\IamSuite\Models\Subscription;
use Filamat\IamSuite\Models\SubscriptionPlan;
use Filamat\IamSuite\Models\Tenant;
use Filamat\IamSuite\Support\TenantContext;
use Haida\FeatureGates\Models\PlanFeature;
use Haida\FeatureGates\Models\TenantFeatureOverride;
use Haida\FeatureGates\Services\FeatureGateService;
use Illuminate\Support\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FeatureGateServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_tenant_override_has_priority(): void
    {
        $tenant = Tenant::query()->create([
            'name' => 'Tenant Gate',
            'slug' => 'tenant-gate',
            'status' => 'active',
        ]);

        TenantContext::setTenant($tenant);

        $plan = SubscriptionPlan::query()->create([
            'tenant_id' => $tenant->getKey(),
            'name' => 'Basic',
            'code' => 'basic-plan',
            'period_days' => 30,
        ]);

        Subscription::query()->create([
            'tenant_id' => $tenant->getKey(),
            'plan_id' => $plan->getKey(),
            'status' => 'active',
        ]);

        TenantFeatureOverride::query()->create([
            'tenant_id' => $tenant->getKey(),
            'feature_key' => 'blog',
            'allowed' => false,
        ]);

        $decision = app(FeatureGateService::class)->evaluate($tenant, 'blog');

        $this->assertFalse($decision->allowed);
        $this->assertSame('tenant_override', $decision->source);

        TenantFeatureOverride::query()->updateOrCreate(
            ['tenant_id' => $tenant->getKey(), 'feature_key' => 'blog'],
            ['allowed' => true],
        );

        $decision = app(FeatureGateService::class)->evaluate($tenant, 'blog');

        $this->assertTrue($decision->allowed);
        $this->assertSame('tenant_override', $decision->source);
    }

    public function test_plan_feature_record_controls_access(): void
    {
        $tenant = Tenant::query()->create([
            'name' => 'Tenant Plan',
            'slug' => 'tenant-plan',
            'status' => 'active',
        ]);

        TenantContext::setTenant($tenant);

        $plan = SubscriptionPlan::query()->create([
            'tenant_id' => $tenant->getKey(),
            'name' => 'Pro',
            'code' => 'pro-plan',
            'period_days' => 30,
        ]);

        Subscription::query()->create([
            'tenant_id' => $tenant->getKey(),
            'plan_id' => $plan->getKey(),
            'status' => 'active',
        ]);

        PlanFeature::query()->create([
            'plan_id' => $plan->getKey(),
            'feature_key' => 'commerce',
            'enabled' => false,
        ]);

        $decision = app(FeatureGateService::class)->evaluate($tenant, 'commerce');
        $this->assertFalse($decision->allowed);
        $this->assertSame('plan_feature', $decision->source);

        PlanFeature::query()->updateOrCreate(
            ['plan_id' => $plan->getKey(), 'feature_key' => 'commerce'],
            ['enabled' => true],
        );

        $decision = app(FeatureGateService::class)->evaluate($tenant, 'commerce');
        $this->assertTrue($decision->allowed);
        $this->assertSame('plan_feature', $decision->source);
    }

    public function test_fallback_uses_plan_features_json(): void
    {
        $tenant = Tenant::query()->create([
            'name' => 'Tenant Json',
            'slug' => 'tenant-json',
            'status' => 'active',
        ]);

        TenantContext::setTenant($tenant);

        $plan = SubscriptionPlan::query()->create([
            'tenant_id' => $tenant->getKey(),
            'name' => 'Json Plan',
            'code' => 'json-plan',
            'period_days' => 30,
            'features' => [
                'features' => ['cms', 'blog'],
            ],
        ]);

        Subscription::query()->create([
            'tenant_id' => $tenant->getKey(),
            'plan_id' => $plan->getKey(),
            'status' => 'active',
        ]);

        $decision = app(FeatureGateService::class)->evaluate($tenant, 'blog');
        $this->assertTrue($decision->allowed);
        $this->assertSame('plan_features_json', $decision->source);
    }

    public function test_plan_feature_window_blocks_access(): void
    {
        $tenant = Tenant::query()->create([
            'name' => 'Tenant Window',
            'slug' => 'tenant-window',
            'status' => 'active',
        ]);

        TenantContext::setTenant($tenant);

        $plan = SubscriptionPlan::query()->create([
            'tenant_id' => $tenant->getKey(),
            'name' => 'Window Plan',
            'code' => 'window-plan',
            'period_days' => 30,
        ]);

        Subscription::query()->create([
            'tenant_id' => $tenant->getKey(),
            'plan_id' => $plan->getKey(),
            'status' => 'active',
        ]);

        PlanFeature::query()->create([
            'plan_id' => $plan->getKey(),
            'feature_key' => 'cms.page.view',
            'enabled' => true,
            'starts_at' => now()->addDay(),
        ]);

        $decision = app(FeatureGateService::class)->evaluate($tenant, 'cms.page.view', null, null, Carbon::now());

        $this->assertFalse($decision->allowed);
        $this->assertSame('plan_feature', $decision->source);
    }

    public function test_override_outside_window_falls_back_to_plan(): void
    {
        $tenant = Tenant::query()->create([
            'name' => 'Tenant Override Window',
            'slug' => 'tenant-override-window',
            'status' => 'active',
        ]);

        TenantContext::setTenant($tenant);

        $plan = SubscriptionPlan::query()->create([
            'tenant_id' => $tenant->getKey(),
            'name' => 'Override Plan',
            'code' => 'override-plan',
            'period_days' => 30,
        ]);

        Subscription::query()->create([
            'tenant_id' => $tenant->getKey(),
            'plan_id' => $plan->getKey(),
            'status' => 'active',
        ]);

        TenantFeatureOverride::query()->create([
            'tenant_id' => $tenant->getKey(),
            'feature_key' => 'blog.post.view',
            'allowed' => false,
            'starts_at' => now()->addDays(2),
        ]);

        PlanFeature::query()->create([
            'plan_id' => $plan->getKey(),
            'feature_key' => 'blog.post.view',
            'enabled' => true,
        ]);

        $decision = app(FeatureGateService::class)->evaluate($tenant, 'blog.post.view', null, null, Carbon::now());

        $this->assertTrue($decision->allowed);
        $this->assertSame('plan_feature', $decision->source);
    }

    public function test_limits_are_returned_from_overrides_and_plan(): void
    {
        $tenant = Tenant::query()->create([
            'name' => 'Tenant Limits',
            'slug' => 'tenant-limits',
            'status' => 'active',
        ]);

        TenantContext::setTenant($tenant);

        $plan = SubscriptionPlan::query()->create([
            'tenant_id' => $tenant->getKey(),
            'name' => 'Limits Plan',
            'code' => 'limits-plan',
            'period_days' => 30,
        ]);

        Subscription::query()->create([
            'tenant_id' => $tenant->getKey(),
            'plan_id' => $plan->getKey(),
            'status' => 'active',
        ]);

        TenantFeatureOverride::query()->create([
            'tenant_id' => $tenant->getKey(),
            'feature_key' => 'sites.create',
            'allowed' => true,
            'limits' => ['max_sites' => 2],
        ]);

        $decision = app(FeatureGateService::class)->evaluate($tenant, 'sites.create');
        $this->assertSame(['max_sites' => 2], $decision->limits);

        TenantFeatureOverride::query()->where('tenant_id', $tenant->getKey())->where('feature_key', 'sites.create')->delete();

        PlanFeature::query()->create([
            'plan_id' => $plan->getKey(),
            'feature_key' => 'sites.create',
            'enabled' => true,
            'limits' => ['max_sites' => 5],
        ]);

        $decision = app(FeatureGateService::class)->evaluate($tenant, 'sites.create');
        $this->assertSame(['max_sites' => 5], $decision->limits);
    }
}
