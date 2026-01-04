<?php

namespace Tests\Feature\FeatureGates;

use App\Models\User;
use Filamat\IamSuite\Models\PermissionOverride;
use Filamat\IamSuite\Models\Subscription;
use Filamat\IamSuite\Models\SubscriptionPlan;
use Filamat\IamSuite\Models\Tenant;
use Filamat\IamSuite\Services\AccessService;
use Haida\FeatureGates\Models\PlanFeature;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AccessServiceGateTest extends TestCase
{
    use RefreshDatabase;

    public function test_feature_gate_blocks_permission_even_with_override(): void
    {
        $tenant = Tenant::query()->create([
            'name' => 'Tenant Gate Access',
            'slug' => 'tenant-gate-access',
            'status' => 'active',
        ]);

        $user = User::query()->create([
            'name' => 'Gate User',
            'email' => 'gate.user@example.test',
            'password' => bcrypt('secret'),
        ]);

        $plan = SubscriptionPlan::query()->create([
            'tenant_id' => $tenant->getKey(),
            'name' => 'Gate Plan',
            'code' => 'gate-plan',
            'period_days' => 30,
            'features' => [
                'permissions' => ['catalog.product.view'],
            ],
        ]);

        Subscription::query()->create([
            'tenant_id' => $tenant->getKey(),
            'plan_id' => $plan->getKey(),
            'status' => 'active',
        ]);

        PermissionOverride::query()->create([
            'tenant_id' => $tenant->getKey(),
            'user_id' => $user->getKey(),
            'permission_key' => 'catalog.product.view',
            'effect' => 'allow',
        ]);

        PlanFeature::query()->create([
            'plan_id' => $plan->getKey(),
            'feature_key' => 'catalog.product.view',
            'enabled' => false,
        ]);

        $allowed = app(AccessService::class)->checkPermission($user, $tenant, 'catalog.product.view');
        $this->assertFalse($allowed);

        PlanFeature::query()->updateOrCreate([
            'plan_id' => $plan->getKey(),
            'feature_key' => 'catalog.product.view',
        ], [
            'enabled' => true,
        ]);

        $allowed = app(AccessService::class)->checkPermission($user, $tenant, 'catalog.product.view');
        $this->assertTrue($allowed);
    }
}
