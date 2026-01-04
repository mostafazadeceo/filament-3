<?php

namespace Tests\Feature\TenancyDomains;

use Filamat\IamSuite\Models\Tenant;
use Filamat\IamSuite\Support\TenantContext;
use Haida\TenancyDomains\Middleware\ResolveTenantFromHost;
use Haida\TenancyDomains\Models\SiteDomain;
use Haida\TenancyDomains\Support\SiteContext;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Tests\TestCase;

class ResolveTenantFromHostTest extends TestCase
{
    use RefreshDatabase;

    public function test_resolves_tenant_by_subdomain(): void
    {
        config([
            'tenancy-domains.root_domain' => 'example.test',
            'tenancy-domains.allowed_hosts' => [],
        ]);

        $tenant = Tenant::query()->create([
            'name' => 'Acme',
            'slug' => 'acme',
            'status' => 'active',
        ]);

        $request = Request::create('http://acme.example.test/', 'GET');
        $middleware = new ResolveTenantFromHost();

        $response = $middleware->handle($request, function () use ($tenant) {
            $this->assertSame($tenant->getKey(), TenantContext::getTenantId());
            $this->assertSame($tenant->getKey(), SiteContext::getTenantId());

            return new Response('ok');
        });

        $this->assertSame('ok', $response->getContent());
    }

    public function test_resolves_tenant_by_verified_domain(): void
    {
        config([
            'tenancy-domains.root_domain' => null,
            'tenancy-domains.allowed_hosts' => [],
        ]);

        $tenant = Tenant::query()->create([
            'name' => 'Beta',
            'slug' => 'beta',
            'status' => 'active',
        ]);

        SiteDomain::query()->create([
            'tenant_id' => $tenant->getKey(),
            'host' => 'store.test',
            'type' => 'custom',
            'status' => 'verified',
            'verified_at' => now(),
        ]);

        $request = Request::create('http://store.test/', 'GET');
        $middleware = new ResolveTenantFromHost();

        $response = $middleware->handle($request, function () use ($tenant) {
            $this->assertSame($tenant->getKey(), TenantContext::getTenantId());
            $this->assertSame('store.test', SiteContext::getHost());

            return new Response('ok');
        });

        $this->assertSame('ok', $response->getContent());
    }

    public function test_unknown_host_is_blocked(): void
    {
        config([
            'tenancy-domains.root_domain' => 'example.test',
            'tenancy-domains.allowed_hosts' => [],
        ]);

        $request = Request::create('http://unknown.test/', 'GET');
        $middleware = new ResolveTenantFromHost();

        $this->expectException(NotFoundHttpException::class);

        $middleware->handle($request, function () {
            return new Response('ok');
        });
    }

    public function test_allowed_host_is_accepted(): void
    {
        config([
            'tenancy-domains.root_domain' => null,
            'tenancy-domains.allowed_hosts' => ['admin.example.test'],
        ]);

        $request = Request::create('http://admin.example.test/', 'GET');
        $middleware = new ResolveTenantFromHost();

        $response = $middleware->handle($request, function () {
            return new Response('ok');
        });

        $this->assertSame('ok', $response->getContent());
    }
}
