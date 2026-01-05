<?php

namespace Tests\Feature\IamSuite;

use App\Models\User;
use Filamat\IamSuite\Models\Organization;
use Filamat\IamSuite\Models\Tenant;
use Filamat\IamSuite\Services\AccessService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrganizationEntitlementAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_entitlements_block_module_permissions(): void
    {
        $organization = Organization::query()->create([
            'name' => 'Org',
            'shared_data_mode' => 'isolated',
            'settings' => [
                'entitlements' => [
                    'status' => 'active',
                    'modules' => ['filamat-iam-suite'],
                ],
            ],
        ]);

        $tenant = Tenant::query()->create([
            'name' => 'Tenant',
            'slug' => 'tenant-entitlement',
            'status' => 'active',
            'organization_id' => $organization->getKey(),
        ]);

        $user = User::query()->create([
            'name' => 'User',
            'email' => 'user@example.test',
            'password' => bcrypt('secret'),
        ]);

        $result = app(AccessService::class)->explainPermission($user, $tenant, 'catalog.product.view');

        $this->assertFalse($result['allowed']);
        $this->assertTrue(collect($result['trace'])->contains(function (array $entry): bool {
            return $entry['source'] === 'organization_entitlement' && $entry['effect'] === 'deny';
        }));
    }
}
