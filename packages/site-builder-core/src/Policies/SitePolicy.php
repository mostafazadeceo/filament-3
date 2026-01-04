<?php

namespace Haida\SiteBuilderCore\Policies;

use App\Models\User;
use Filamat\IamSuite\Support\IamAuthorization;
use Haida\SiteBuilderCore\Models\Site;

class SitePolicy
{
    public function viewAny(User $user): bool
    {
        return IamAuthorization::allowsAny([
            'site_builder.site.view',
            'site_builder.site.manage',
        ], null, $user);
    }

    public function view(User $user, Site $site): bool
    {
        return IamAuthorization::allowsAny([
            'site_builder.site.view',
            'site_builder.site.manage',
        ], IamAuthorization::resolveTenantFromRecord($site), $user);
    }

    public function create(User $user): bool
    {
        return IamAuthorization::allows('site_builder.site.manage', null, $user);
    }

    public function update(User $user, Site $site): bool
    {
        return IamAuthorization::allows('site_builder.site.manage', IamAuthorization::resolveTenantFromRecord($site), $user);
    }

    public function delete(User $user, Site $site): bool
    {
        return IamAuthorization::allows('site_builder.site.manage', IamAuthorization::resolveTenantFromRecord($site), $user);
    }

    public function restore(User $user, Site $site): bool
    {
        return IamAuthorization::allows('site_builder.site.manage', IamAuthorization::resolveTenantFromRecord($site), $user);
    }

    public function forceDelete(User $user, Site $site): bool
    {
        return IamAuthorization::allows('site_builder.site.manage', IamAuthorization::resolveTenantFromRecord($site), $user);
    }
}
