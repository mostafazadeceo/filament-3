<?php

namespace Haida\TenancyDomains\Policies;

use Filamat\IamSuite\Support\IamAuthorization;
use Haida\TenancyDomains\Models\SiteDomain;
use Illuminate\Contracts\Auth\Authenticatable;

class SiteDomainPolicy
{
    public function viewAny(?Authenticatable $user = null): bool
    {
        return IamAuthorization::allows('site.domain.view', null, $user);
    }

    public function view(?Authenticatable $user, SiteDomain $domain): bool
    {
        return IamAuthorization::allows('site.domain.view', IamAuthorization::resolveTenantFromRecord($domain), $user);
    }

    public function create(?Authenticatable $user = null): bool
    {
        return IamAuthorization::allows('site.domain.manage', null, $user);
    }

    public function update(?Authenticatable $user, SiteDomain $domain): bool
    {
        return IamAuthorization::allows('site.domain.manage', IamAuthorization::resolveTenantFromRecord($domain), $user);
    }

    public function delete(?Authenticatable $user, SiteDomain $domain): bool
    {
        return IamAuthorization::allows('site.domain.manage', IamAuthorization::resolveTenantFromRecord($domain), $user);
    }

    public function requestTls(?Authenticatable $user, SiteDomain $domain): bool
    {
        if (! $domain->verified_at) {
            return false;
        }

        return IamAuthorization::allows('site.domain.manage', IamAuthorization::resolveTenantFromRecord($domain), $user);
    }
}
