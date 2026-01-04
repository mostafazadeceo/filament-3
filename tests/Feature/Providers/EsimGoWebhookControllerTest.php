<?php

declare(strict_types=1);

namespace Tests\Feature\Providers;

use Filamat\IamSuite\Models\Tenant;
use Haida\ProvidersEsimGoCore\Models\EsimGoCallback;
use Haida\ProvidersEsimGoCore\Models\EsimGoConnection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EsimGoWebhookControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_callback_valid_signature_is_processed(): void
    {
        $tenant = Tenant::query()->create([
            'name' => 'Tenant B',
            'slug' => 'tenant-b',
            'status' => 'active',
        ]);

        $connection = EsimGoConnection::query()->create([
            'tenant_id' => $tenant->getKey(),
            'name' => 'Default',
            'api_key' => 'secret-key',
            'status' => 'active',
        ]);

        $payload = [
            'eventType' => 'bundle_usage',
            'iccid' => '890123',
            'bundle' => [
                'reference' => 'EU-1GB',
            ],
            'remainingQuantity' => 512,
        ];

        $raw = json_encode($payload);
        $signature = base64_encode(hash_hmac('sha256', $raw, 'secret-key', true));

        $response = $this->postJson('/api/v1/providers/esim-go/callback?connection_id='.$connection->getKey(), $payload, [
            'X-ESIMGO-SIGNATURE' => $signature,
        ]);

        $response->assertOk();

        $this->assertDatabaseHas('esim_go_callbacks', [
            'tenant_id' => $tenant->getKey(),
            'iccid' => '890123',
        ]);
    }

    public function test_location_event_is_ignored(): void
    {
        $tenant = Tenant::query()->create([
            'name' => 'Tenant C',
            'slug' => 'tenant-c',
            'status' => 'active',
        ]);

        $connection = EsimGoConnection::query()->create([
            'tenant_id' => $tenant->getKey(),
            'name' => 'Default',
            'api_key' => 'secret-key',
            'status' => 'active',
        ]);

        $payload = [
            'eventType' => 'location_update',
            'iccid' => '890999',
        ];

        $raw = json_encode($payload);
        $signature = base64_encode(hash_hmac('sha256', $raw, 'secret-key', true));

        $response = $this->postJson('/api/v1/providers/esim-go/callback?connection_id='.$connection->getKey(), $payload, [
            'X-ESIMGO-SIGNATURE' => $signature,
        ]);

        $response->assertOk();
        $this->assertSame(0, EsimGoCallback::query()->count());
    }
}
