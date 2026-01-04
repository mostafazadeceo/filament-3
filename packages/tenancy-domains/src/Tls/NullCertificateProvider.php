<?php

declare(strict_types=1);

namespace Haida\TenancyDomains\Tls;

use Haida\TenancyDomains\Models\SiteDomain;

class NullCertificateProvider implements CertificateProvider
{
    public function issue(SiteDomain $domain): CertificateResult
    {
        return CertificateResult::failed('TLS provider is not configured.');
    }
}
