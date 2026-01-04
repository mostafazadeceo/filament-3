<?php

namespace Haida\FilamentThreeCx\Tests\Feature;

use Filamat\IamSuite\Models\Tenant;
use Filamat\IamSuite\Support\TenantContext;
use Haida\FilamentThreeCx\Clients\XapiClient;
use Haida\FilamentThreeCx\Exceptions\ThreeCxRateLimitException;
use Haida\FilamentThreeCx\Models\ThreeCxInstance;
use Haida\FilamentThreeCx\Tests\TestCase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class ThreeCxClientRateLimitTest extends TestCase
{
    public function test_rate_limit_exception_is_thrown(): void
    {
        Http::preventStrayRequests();

        Http::fake(function ($request) {
            if (str_ends_with($request->url(), '/connect/token')) {
                return Http::response([
                    'access_token' => 'token-1',
                    'expires_in' => 3600,
                ], 200);
            }

            if (str_contains($request->url(), '/xapi/contacts')) {
                return Http::response(['message' => 'Too Many Requests'], 429);
            }

            return Http::response([], 404);
        });

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

        $client = app(XapiClient::class, ['instance' => $instance]);

        $this->expectException(ThreeCxRateLimitException::class);
        $client->request('GET', '/contacts');
    }
}
