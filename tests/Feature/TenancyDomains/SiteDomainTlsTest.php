<?php

namespace Tests\Feature\TenancyDomains;

use Filamat\IamSuite\Models\Tenant;
use Filamat\IamSuite\Support\TenantContext;
use Haida\TenancyDomains\Models\SiteDomain;
use Haida\TenancyDomains\Services\SiteDomainService;
use Haida\TenancyDomains\Tls\CertificateProvider;
use Haida\TenancyDomains\Tls\CertificateResult;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SiteDomainTlsTest extends TestCase
{
    use RefreshDatabase;

    public function test_request_tls_updates_status(): void
    {
        config([
            'tenancy-domains.tls.provider' => 'test',
            'tenancy-domains.tls.providers' => [
                'test' => TestCertificateProvider::class,
            ],
        ]);

        $this->app->bind(TestCertificateProvider::class, fn () => new TestCertificateProvider());

        $tenant = Tenant::query()->create([
            'name' => 'Tenant TLS',
            'slug' => 'tenant-tls',
            'status' => 'active',
        ]);

        TenantContext::setTenant($tenant);

        $domain = SiteDomain::query()->create([
            'tenant_id' => $tenant->getKey(),
            'host' => 'example.test',
            'type' => 'custom',
            'status' => SiteDomain::STATUS_VERIFIED,
            'verified_at' => now(),
        ]);

        $service = app(SiteDomainService::class);
        $domain = $service->requestTls($domain, 'test', 'dns-01');

        $this->assertSame(SiteDomain::TLS_STATUS_ISSUED, $domain->tls_status);
        $this->assertSame('test', $domain->tls_provider);
        $this->assertNotNull($domain->tls_issued_at);
        $this->assertNotNull($domain->tls_expires_at);
    }
}

class TestCertificateProvider implements CertificateProvider
{
    public function issue(SiteDomain $domain): CertificateResult
    {
        return CertificateResult::issued(now(), now()->addDays(90));
    }
}
