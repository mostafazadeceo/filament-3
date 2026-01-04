<?php

namespace Haida\ContentCms\Policies;

use App\Models\User;
use Filamat\IamSuite\Support\IamAuthorization;
use Haida\ContentCms\Models\CmsPage;

class CmsPagePolicy
{
    public function viewAny(User $user): bool
    {
        return IamAuthorization::allowsAny([
            'cms.page.view',
            'cms.page.manage',
        ], null, $user);
    }

    public function view(User $user, CmsPage $page): bool
    {
        return IamAuthorization::allowsAny([
            'cms.page.view',
            'cms.page.manage',
        ], IamAuthorization::resolveTenantFromRecord($page), $user);
    }

    public function create(User $user): bool
    {
        return IamAuthorization::allows('cms.page.manage', null, $user);
    }

    public function update(User $user, CmsPage $page): bool
    {
        return IamAuthorization::allows('cms.page.manage', IamAuthorization::resolveTenantFromRecord($page), $user);
    }

    public function delete(User $user, CmsPage $page): bool
    {
        return IamAuthorization::allows('cms.page.manage', IamAuthorization::resolveTenantFromRecord($page), $user);
    }

    public function restore(User $user, CmsPage $page): bool
    {
        return IamAuthorization::allows('cms.page.manage', IamAuthorization::resolveTenantFromRecord($page), $user);
    }

    public function forceDelete(User $user, CmsPage $page): bool
    {
        return IamAuthorization::allows('cms.page.manage', IamAuthorization::resolveTenantFromRecord($page), $user);
    }
}
