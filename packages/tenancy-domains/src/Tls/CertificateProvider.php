<?php

declare(strict_types=1);

namespace Haida\TenancyDomains\Tls;

use Haida\TenancyDomains\Models\SiteDomain;

interface CertificateProvider
{
    public function issue(SiteDomain $domain): CertificateResult;
}
