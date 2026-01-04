<?php

namespace Haida\Blog\Policies;

use App\Models\User;
use Filamat\IamSuite\Support\IamAuthorization;
use Haida\Blog\Models\BlogCategory;

class BlogCategoryPolicy
{
    public function viewAny(User $user): bool
    {
        return IamAuthorization::allows('blog.category.manage', null, $user);
    }

    public function view(User $user, BlogCategory $category): bool
    {
        return IamAuthorization::allows('blog.category.manage', IamAuthorization::resolveTenantFromRecord($category), $user);
    }

    public function create(User $user): bool
    {
        return IamAuthorization::allows('blog.category.manage', null, $user);
    }

    public function update(User $user, BlogCategory $category): bool
    {
        return IamAuthorization::allows('blog.category.manage', IamAuthorization::resolveTenantFromRecord($category), $user);
    }

    public function delete(User $user, BlogCategory $category): bool
    {
        return IamAuthorization::allows('blog.category.manage', IamAuthorization::resolveTenantFromRecord($category), $user);
    }
}
