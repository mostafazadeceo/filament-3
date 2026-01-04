<?php

namespace Haida\FilamentThreeCx\Tests\Feature;

use Filamat\IamSuite\Models\Tenant;
use Filamat\IamSuite\Support\TenantContext;
use Haida\FilamentThreeCx\Models\ThreeCxInstance;
use Haida\FilamentThreeCx\Services\ThreeCxAuthService;
use Haida\FilamentThreeCx\Tests\TestCase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class ThreeCxClientTokenTest extends TestCase
{
    public function test_token_is_cached(): void
    {
        Http::fake([
            'https://threecx.test/connect/token' => Http::response([
                'access_token' => 'token-1',
                'expires_in' => 3600,
            ], 200),
        ]);

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
            'client_id' => 'client',
            'client_secret' => 'secret',
            'xapi_enabled' => true,
        ]);

        $auth = app(ThreeCxAuthService::class);
        $token1 = $auth->getAccessToken($instance, 'xapi');
        $token2 = $auth->getAccessToken($instance, 'xapi');

        $this->assertSame('token-1', $token1);
        $this->assertSame('token-1', $token2);
        Http::assertSentCount(1);
    }
}
