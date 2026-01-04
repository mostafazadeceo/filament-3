<?php

declare(strict_types=1);

namespace Tests\Feature\Crypto;

use Laravel\Sanctum\Sanctum;

class CryptoPayoutDestinationApiTest extends CryptoApiTestCase
{
    public function test_can_manage_payout_destinations_via_api(): void
    {
        $tenant = $this->createTenant('Tenant Crypto Destinations');

        $user = $this->createUserWithPermissions($tenant, [
            'crypto.payout_destinations.manage',
            'crypto.payout_destinations.view',
        ]);

        Sanctum::actingAs($user, [
            'crypto.payout_destinations.manage',
            'crypto.payout_destinations.view',
            'tenant:'.$tenant->getKey(),
        ]);

        $create = $this->postJson('/api/v1/crypto/payout-destinations', [
            'label' => 'Main',
            'address' => 'ADDR-1',
            'currency' => 'USDT',
            'network' => 'TRC20',
            'status' => 'active',
        ], [
            'X-Tenant-ID' => $tenant->getKey(),
        ]);

        $create->assertCreated();
        $destinationId = $create->json('data.id');

        $list = $this->getJson('/api/v1/crypto/payout-destinations', [
            'X-Tenant-ID' => $tenant->getKey(),
        ]);

        $list->assertOk();
        $list->assertJsonFragment([
            'id' => $destinationId,
            'address' => 'ADDR-1',
        ]);

        $update = $this->putJson('/api/v1/crypto/payout-destinations/'.$destinationId, [
            'status' => 'inactive',
        ], [
            'X-Tenant-ID' => $tenant->getKey(),
        ]);

        $update->assertOk();
        $update->assertJsonPath('data.status', 'inactive');

        $delete = $this->deleteJson('/api/v1/crypto/payout-destinations/'.$destinationId, [], [
            'X-Tenant-ID' => $tenant->getKey(),
        ]);

        $delete->assertOk();
    }
}
