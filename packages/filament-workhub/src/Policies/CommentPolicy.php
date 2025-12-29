<?php

namespace Haida\FilamentWorkhub\Policies;

use App\Models\User;
use Filamat\IamSuite\Support\IamAuthorization;
use Haida\FilamentWorkhub\Models\Comment;

class CommentPolicy
{
    public function viewAny(User $user): bool
    {
        return IamAuthorization::allowsAny([
            'workhub.comment.view',
            'workhub.comment.manage',
        ], null, $user);
    }

    public function view(User $user, Comment $comment): bool
    {
        return IamAuthorization::allowsAny([
            'workhub.comment.view',
            'workhub.comment.manage',
        ], IamAuthorization::resolveTenantFromRecord($comment), $user);
    }

    public function create(User $user): bool
    {
        return IamAuthorization::allows('workhub.comment.manage', null, $user);
    }

    public function update(User $user, Comment $comment): bool
    {
        return IamAuthorization::allows('workhub.comment.manage', IamAuthorization::resolveTenantFromRecord($comment), $user);
    }

    public function delete(User $user, Comment $comment): bool
    {
        return IamAuthorization::allows('workhub.comment.manage', IamAuthorization::resolveTenantFromRecord($comment), $user);
    }
}
