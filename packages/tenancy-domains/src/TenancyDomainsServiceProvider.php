<?php

namespace Haida\TenancyDomains;

use Filamat\IamSuite\Contracts\CapabilityRegistryInterface;
use Haida\TenancyDomains\Console\RenewCertificatesCommand;
use Haida\TenancyDomains\Middleware\RequireService;
use Haida\TenancyDomains\Middleware\ResolveTenantFromHost;
use Haida\TenancyDomains\Models\SiteDomain;
use Haida\TenancyDomains\Policies\SiteDomainPolicy;
use Haida\TenancyDomains\Services\CertificateManager;
use Haida\TenancyDomains\Services\DomainVerificationService;
use Haida\TenancyDomains\Services\SiteDomainService;
use Haida\TenancyDomains\Support\TenancyDomainsCapabilities;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class TenancyDomainsServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('tenancy-domains')
            ->hasConfigFile('tenancy-domains')
            ->hasRoutes('api')
            ->hasMigrations([
                '2025_12_30_000004_create_site_domains_table',
                '2025_12_30_000021_add_tls_fields_to_site_domains_table',
            ])
            ->hasCommands([
                RenewCertificatesCommand::class,
            ])
            ->runsMigrations();
    }

    public function packageRegistered(): void
    {
        $this->app->singleton(DomainVerificationService::class);
        $this->app->singleton(SiteDomainService::class);
        $this->app->singleton(CertificateManager::class);
    }

    public function packageBooted(): void
    {
        Route::aliasMiddleware('resolve.site', ResolveTenantFromHost::class);
        Route::aliasMiddleware('require.service', RequireService::class);

        Gate::policy(SiteDomain::class, SiteDomainPolicy::class);

        if (interface_exists(CapabilityRegistryInterface::class)) {
            $registry = $this->app->make(CapabilityRegistryInterface::class);
            TenancyDomainsCapabilities::register($registry);
        }
    }
}
