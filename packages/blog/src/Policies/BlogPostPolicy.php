<?php

namespace Haida\Blog\Policies;

use App\Models\User;
use Filamat\IamSuite\Support\IamAuthorization;
use Haida\Blog\Models\BlogPost;

class BlogPostPolicy
{
    public function viewAny(User $user): bool
    {
        return IamAuthorization::allowsAny([
            'blog.post.view',
            'blog.post.manage',
        ], null, $user);
    }

    public function view(User $user, BlogPost $post): bool
    {
        return IamAuthorization::allowsAny([
            'blog.post.view',
            'blog.post.manage',
        ], IamAuthorization::resolveTenantFromRecord($post), $user);
    }

    public function create(User $user): bool
    {
        return IamAuthorization::allows('blog.post.manage', null, $user);
    }

    public function update(User $user, BlogPost $post): bool
    {
        return IamAuthorization::allows('blog.post.manage', IamAuthorization::resolveTenantFromRecord($post), $user);
    }

    public function delete(User $user, BlogPost $post): bool
    {
        return IamAuthorization::allows('blog.post.manage', IamAuthorization::resolveTenantFromRecord($post), $user);
    }

    public function restore(User $user, BlogPost $post): bool
    {
        return IamAuthorization::allows('blog.post.manage', IamAuthorization::resolveTenantFromRecord($post), $user);
    }

    public function forceDelete(User $user, BlogPost $post): bool
    {
        return IamAuthorization::allows('blog.post.manage', IamAuthorization::resolveTenantFromRecord($post), $user);
    }
}
