<?php

declare(strict_types=1);

namespace Haida\SmsBulk\Tests\Integration;

use Filamat\IamSuite\Models\Tenant;
use Filamat\IamSuite\Support\TenantContext;
use Haida\SmsBulk\Clients\Exceptions\IppanelAuthException;
use Haida\SmsBulk\Clients\Exceptions\IppanelTransportException;
use Haida\SmsBulk\Clients\Exceptions\IppanelValidationException;
use Haida\SmsBulk\Clients\IppanelEdgeClient;
use Haida\SmsBulk\Models\SmsBulkProviderConnection;
use Haida\SmsBulk\Tests\TestCase;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;

class IppanelEdgeClientTest extends TestCase
{
    public function test_my_credit_sets_authorization_header_and_correlation_id(): void
    {
        Http::fake(function ($request) {
            $this->assertSame('my-secret-token', $request->header('Authorization')[0] ?? null);
            $this->assertNotEmpty($request->header('X-Correlation-Id')[0] ?? null);
            $this->assertStringContainsString('/api/payment/my-credit', $request->url());

            return Http::response([
                'data' => ['credit' => 250000],
                'meta' => ['status' => true],
            ], 200);
        });

        $tenant = Tenant::create(['name' => 'Tenant A', 'slug' => 'tenant-a']);
        TenantContext::setTenant($tenant);

        $connection = SmsBulkProviderConnection::create([
            'tenant_id' => $tenant->getKey(),
            'provider' => 'ippanel_edge',
            'display_name' => 'Primary Edge',
            'base_url_override' => 'https://edge.example.test/v1',
            'encrypted_token' => 'my-secret-token',
            'status' => 'active',
        ]);

        $client = app(IppanelEdgeClient::class, ['connection' => $connection]);
        $response = $client->myCredit();

        $this->assertSame(200, $response['status_code']);
        $this->assertSame(250000, $response['data']['credit']);
    }

    public function test_it_maps_401_to_auth_exception(): void
    {
        Http::fake([
            '*' => Http::response([
                'data' => null,
                'meta' => ['status' => false, 'message' => 'unauthorized'],
            ], 401),
        ]);

        $tenant = Tenant::create(['name' => 'Tenant B', 'slug' => 'tenant-b']);
        TenantContext::setTenant($tenant);

        $connection = SmsBulkProviderConnection::create([
            'tenant_id' => $tenant->getKey(),
            'provider' => 'ippanel_edge',
            'display_name' => 'Primary Edge',
            'base_url_override' => 'https://edge.example.test/v1',
            'encrypted_token' => 'bad-token',
            'status' => 'active',
        ]);

        $client = app(IppanelEdgeClient::class, ['connection' => $connection]);

        $this->expectException(IppanelAuthException::class);
        $client->myCredit();
    }

    public function test_it_maps_422_to_validation_exception(): void
    {
        Http::fake([
            '*' => Http::response([
                'data' => null,
                'meta' => ['status' => false, 'message' => 'invalid request'],
            ], 422),
        ]);

        $tenant = Tenant::create(['name' => 'Tenant C', 'slug' => 'tenant-c']);
        TenantContext::setTenant($tenant);

        $connection = SmsBulkProviderConnection::create([
            'tenant_id' => $tenant->getKey(),
            'provider' => 'ippanel_edge',
            'display_name' => 'Primary Edge',
            'base_url_override' => 'https://edge.example.test/v1',
            'encrypted_token' => 'token-1',
            'status' => 'active',
        ]);

        $client = app(IppanelEdgeClient::class, ['connection' => $connection]);

        $this->expectException(IppanelValidationException::class);
        $client->calculatePrice(['number' => '+983000505', 'message' => '']);
    }

    public function test_it_maps_connection_timeout_to_transport_exception(): void
    {
        Http::fake(function () {
            throw new ConnectionException('Connection timed out');
        });

        $tenant = Tenant::create(['name' => 'Tenant D', 'slug' => 'tenant-d']);
        TenantContext::setTenant($tenant);

        $connection = SmsBulkProviderConnection::create([
            'tenant_id' => $tenant->getKey(),
            'provider' => 'ippanel_edge',
            'display_name' => 'Primary Edge',
            'base_url_override' => 'https://edge.example.test/v1',
            'encrypted_token' => 'token-2',
            'status' => 'active',
        ]);

        $client = app(IppanelEdgeClient::class, ['connection' => $connection]);

        $this->expectException(IppanelTransportException::class);
        $client->myCredit();
    }
}
