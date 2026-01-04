<?php

namespace Haida\PageBuilder\Policies;

use App\Models\User;
use Filamat\IamSuite\Support\IamAuthorization;
use Haida\PageBuilder\Models\PageTemplate;

class PageTemplatePolicy
{
    public function viewAny(User $user): bool
    {
        return IamAuthorization::allowsAny([
            'page_builder.template.view',
            'page_builder.template.manage',
        ], null, $user);
    }

    public function view(User $user, PageTemplate $template): bool
    {
        return IamAuthorization::allowsAny([
            'page_builder.template.view',
            'page_builder.template.manage',
        ], IamAuthorization::resolveTenantFromRecord($template), $user);
    }

    public function create(User $user): bool
    {
        return IamAuthorization::allows('page_builder.template.manage', null, $user);
    }

    public function update(User $user, PageTemplate $template): bool
    {
        return IamAuthorization::allows('page_builder.template.manage', IamAuthorization::resolveTenantFromRecord($template), $user);
    }

    public function delete(User $user, PageTemplate $template): bool
    {
        return IamAuthorization::allows('page_builder.template.manage', IamAuthorization::resolveTenantFromRecord($template), $user);
    }

    public function restore(User $user, PageTemplate $template): bool
    {
        return IamAuthorization::allows('page_builder.template.manage', IamAuthorization::resolveTenantFromRecord($template), $user);
    }

    public function forceDelete(User $user, PageTemplate $template): bool
    {
        return IamAuthorization::allows('page_builder.template.manage', IamAuthorization::resolveTenantFromRecord($template), $user);
    }
}
