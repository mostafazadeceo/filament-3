<?php

namespace Haida\FilamentThreeCx\Tests\Feature;

use Filamat\IamSuite\Models\Tenant;
use Filamat\IamSuite\Support\TenantContext;
use Haida\FilamentThreeCx\Clients\XapiClient;
use Haida\FilamentThreeCx\Models\ThreeCxInstance;
use Haida\FilamentThreeCx\Tests\TestCase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class ThreeCxClientRetryTest extends TestCase
{
    public function test_unauthorized_response_triggers_single_refresh(): void
    {
        $tokenCalls = 0;
        $healthCalls = 0;

        Http::fake(function ($request) use (&$tokenCalls, &$healthCalls) {
            if (str_ends_with($request->url(), '/connect/token')) {
                $tokenCalls++;

                return Http::response([
                    'access_token' => 'token-'.$tokenCalls,
                    'expires_in' => 3600,
                ], 200);
            }

            if (str_ends_with($request->url(), '/xapi/health')) {
                $healthCalls++;
                if ($healthCalls === 1) {
                    return Http::response([], 401);
                }

                return Http::response(['ok' => true], 200);
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
        $client->health();

        $this->assertSame(2, $tokenCalls);
        $this->assertSame(2, $healthCalls);
    }
}
