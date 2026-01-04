<?php

namespace Tests\Feature\TenancyDomains;

use Filamat\IamSuite\Models\Tenant;
use Filamat\IamSuite\Support\TenantContext;
use Haida\TenancyDomains\Models\SiteDomain;
use Haida\TenancyDomains\Services\DomainVerificationService;
use Haida\TenancyDomains\Services\SiteDomainService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SiteDomainVerificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_request_verification_generates_token(): void
    {
        $tenant = Tenant::query()->create([
            'name' => 'Tenant Domain',
            'slug' => 'tenant-domain',
            'status' => 'active',
        ]);

        TenantContext::setTenant($tenant);

        $domain = SiteDomain::query()->create([
            'tenant_id' => $tenant->getKey(),
            'host' => 'example.test',
            'type' => 'custom',
        ]);

        $service = app(SiteDomainService::class);
        $domain = $service->requestVerification($domain, 'txt');

        $this->assertNotEmpty($domain->dns_token);
        $this->assertSame(SiteDomain::STATUS_PENDING, $domain->status);
        $this->assertSame('txt', $domain->verification_method);
    }

    public function test_verify_marks_domain_verified(): void
    {
        $tenant = Tenant::query()->create([
            'name' => 'Tenant Verify',
            'slug' => 'tenant-verify',
            'status' => 'active',
        ]);

        TenantContext::setTenant($tenant);

        $domain = SiteDomain::query()->create([
            'tenant_id' => $tenant->getKey(),
            'host' => 'example.test',
            'type' => 'custom',
            'verification_method' => 'txt',
            'dns_token' => 'token-123',
            'status' => SiteDomain::STATUS_PENDING,
        ]);

        $this->app->instance(DomainVerificationService::class, new class extends DomainVerificationService {
            public function verifyTxt(SiteDomain $domain, string $expectedToken): bool
            {
                return true;
            }
        });

        $service = app(SiteDomainService::class);
        $domain = $service->verify($domain);

        $this->assertSame(SiteDomain::STATUS_VERIFIED, $domain->status);
        $this->assertNotNull($domain->verified_at);
        $this->assertNotNull($domain->last_checked_at);
    }

    public function test_verify_marks_domain_failed(): void
    {
        $tenant = Tenant::query()->create([
            'name' => 'Tenant Fail',
            'slug' => 'tenant-fail',
            'status' => 'active',
        ]);

        TenantContext::setTenant($tenant);

        $domain = SiteDomain::query()->create([
            'tenant_id' => $tenant->getKey(),
            'host' => 'example.test',
            'type' => 'custom',
            'verification_method' => 'txt',
            'dns_token' => 'token-456',
            'status' => SiteDomain::STATUS_PENDING,
        ]);

        $this->app->instance(DomainVerificationService::class, new class extends DomainVerificationService {
            public function verifyTxt(SiteDomain $domain, string $expectedToken): bool
            {
                return false;
            }
        });

        $service = app(SiteDomainService::class);
        $domain = $service->verify($domain);

        $this->assertSame(SiteDomain::STATUS_FAILED, $domain->status);
        $this->assertNull($domain->verified_at);
        $this->assertNotNull($domain->last_checked_at);
    }
}
