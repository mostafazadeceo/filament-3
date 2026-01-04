<?php

namespace Haida\Blog\Policies;

use App\Models\User;
use Filamat\IamSuite\Support\IamAuthorization;
use Haida\Blog\Models\BlogTag;

class BlogTagPolicy
{
    public function viewAny(User $user): bool
    {
        return IamAuthorization::allows('blog.tag.manage', null, $user);
    }

    public function view(User $user, BlogTag $tag): bool
    {
        return IamAuthorization::allows('blog.tag.manage', IamAuthorization::resolveTenantFromRecord($tag), $user);
    }

    public function create(User $user): bool
    {
        return IamAuthorization::allows('blog.tag.manage', null, $user);
    }

    public function update(User $user, BlogTag $tag): bool
    {
        return IamAuthorization::allows('blog.tag.manage', IamAuthorization::resolveTenantFromRecord($tag), $user);
    }

    public function delete(User $user, BlogTag $tag): bool
    {
        return IamAuthorization::allows('blog.tag.manage', IamAuthorization::resolveTenantFromRecord($tag), $user);
    }
}
