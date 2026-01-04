<?php

declare(strict_types=1);

namespace Haida\FilamentAppApi\Tests\Feature;

use Filamat\IamSuite\Models\ApiKey;
use Filamat\IamSuite\Models\Tenant;
use Haida\FilamentAppApi\Models\AppSyncChange;
use Haida\FilamentAppApi\Tests\TestCase;
use Illuminate\Support\Str;

class AppSyncPushTest extends TestCase
{
    public function test_sync_push_records_change(): void
    {
        $token = 'sync-key';
        $tenant = Tenant::create([
            'name' => 'Tenant',
            'slug' => Str::random(8),
        ]);

        ApiKey::create([
            'tenant_id' => $tenant->getKey(),
            'name' => 'Test Key',
            'token_hash' => hash('sha256', $token),
            'abilities' => ['app.sync'],
        ]);

        $payload = [
            'items' => [
                [
                    'id' => 'req-1',
                    'module' => 'pos',
                    'action' => 'order.create',
                    'payload' => [
                        'record_id' => 'order-1',
                        'total' => 120000,
                    ],
                ],
            ],
        ];

        $response = $this->postJson('/api/v1/app/sync/push', $payload, [
            'X-Api-Key' => $token,
        ]);

        $response->assertOk();
        $response->assertJsonPath('results.0.status', 'accepted');

        $this->assertDatabaseHas('app_sync_changes', [
            'tenant_id' => $tenant->getKey(),
            'module' => 'pos',
            'record_id' => 'order-1',
        ]);
    }
}
