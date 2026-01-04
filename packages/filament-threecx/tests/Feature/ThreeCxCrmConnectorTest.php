<?php

namespace Haida\FilamentThreeCx\Tests\Feature;

use Filamat\IamSuite\Models\Tenant;
use Filamat\IamSuite\Support\TenantContext;
use Haida\FilamentThreeCx\Models\ThreeCxCallLog;
use Haida\FilamentThreeCx\Models\ThreeCxContact;
use Haida\FilamentThreeCx\Models\ThreeCxInstance;
use Haida\FilamentThreeCx\Tests\TestCase;
use Illuminate\Support\Str;

class ThreeCxCrmConnectorTest extends TestCase
{
    public function test_connector_requires_key(): void
    {
        $this->createInstanceWithKey('secret-key');

        $response = $this->getJson('/api/v1/threecx/crm/lookup?phone=09120000000');
        $response->assertStatus(401);
    }

    public function test_lookup_returns_contacts(): void
    {
        [$instance, $key] = $this->createInstanceWithKey('secret-key');

        ThreeCxContact::create([
            'tenant_id' => $instance->tenant_id,
            'instance_id' => $instance->getKey(),
            'name' => 'مهدی',
            'phones' => ['09120000000'],
            'emails' => ['mehdi@example.com'],
            'external_id' => 'crm-1',
        ]);

        $response = $this->getJson('/api/v1/threecx/crm/lookup?phone=09120000000', [
            'X-ThreeCX-Connector-Key' => $key,
        ]);

        $response->assertOk();
        $response->assertJsonCount(1, 'data');
    }

    public function test_search_returns_contacts(): void
    {
        [$instance, $key] = $this->createInstanceWithKey('secret-key');

        ThreeCxContact::create([
            'tenant_id' => $instance->tenant_id,
            'instance_id' => $instance->getKey(),
            'name' => 'Reza',
            'phones' => ['09121111111'],
            'emails' => ['reza@example.com'],
            'external_id' => 'crm-2',
        ]);

        $response = $this->getJson('/api/v1/threecx/crm/search?q=Reza', [
            'X-ThreeCX-Connector-Key' => $key,
        ]);

        $response->assertOk();
        $response->assertJsonCount(1, 'data');
    }

    public function test_journal_call_creates_log(): void
    {
        [$instance, $key] = $this->createInstanceWithKey('secret-key');

        $response = $this->postJson('/api/v1/threecx/crm/journal/call', [
            'from' => '1001',
            'to' => '1002',
            'status' => 'missed',
            'external_id' => 'call-1',
        ], [
            'X-ThreeCX-Connector-Key' => $key,
        ]);

        $response->assertStatus(201);
        $this->assertSame(1, ThreeCxCallLog::query()->count());
        $this->assertSame('1001', ThreeCxCallLog::query()->first()->from_number);
    }

    public function test_connector_rejects_invalid_key(): void
    {
        $this->createInstanceWithKey('secret-key');

        $response = $this->getJson('/api/v1/threecx/crm/lookup?phone=09120000000', [
            'X-ThreeCX-Connector-Key' => 'wrong-key',
        ]);

        $response->assertStatus(403);
    }

    /**
     * @return array{0:ThreeCxInstance,1:string}
     */
    protected function createInstanceWithKey(string $key): array
    {
        config(['filament-threecx.crm_connector.auth_mode' => 'connector_key']);

        $tenant = Tenant::create([
            'name' => 'Tenant',
            'slug' => Str::random(8),
        ]);

        TenantContext::setTenant($tenant);

        $instance = ThreeCxInstance::create([
            'tenant_id' => $tenant->getKey(),
            'name' => 'Instance',
            'base_url' => 'https://threecx.test',
            'verify_tls' => true,
            'crm_connector_enabled' => true,
            'crm_connector_key' => $key,
            'crm_connector_key_hash' => hash('sha256', $key),
        ]);

        return [$instance, $key];
    }
}
