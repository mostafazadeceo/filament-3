<?php

declare(strict_types=1);

namespace Haida\TenancyDomains\Services;

use Haida\TenancyDomains\Models\SiteDomain;
use Haida\TenancyDomains\Tls\CertificateProvider;
use Haida\TenancyDomains\Tls\CertificateResult;
use Haida\TenancyDomains\Tls\NullCertificateProvider;
use Illuminate\Contracts\Container\Container;

class CertificateManager
{
    public function __construct(private readonly Container $container) {}

    public function issue(SiteDomain $domain, ?string $providerKey = null): CertificateResult
    {
        return $this->resolveProvider($providerKey)->issue($domain);
    }

    public function resolveProvider(?string $providerKey = null): CertificateProvider
    {
        $providerKey = $providerKey ?: config('tenancy-domains.tls.provider', 'null');
        $providers = config('tenancy-domains.tls.providers', []);
        $providerClass = $providers[$providerKey] ?? NullCertificateProvider::class;

        return $this->container->make($providerClass);
    }
}
