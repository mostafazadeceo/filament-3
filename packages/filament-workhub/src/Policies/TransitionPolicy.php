<?php

namespace Haida\FilamentWorkhub\Policies;

use App\Models\User;
use Filamat\IamSuite\Support\IamAuthorization;
use Haida\FilamentWorkhub\Models\Transition;

class TransitionPolicy
{
    public function viewAny(User $user): bool
    {
        return IamAuthorization::allowsAny([
            'workhub.transition.view',
            'workhub.transition.manage',
        ], null, $user);
    }

    public function view(User $user, Transition $transition): bool
    {
        return IamAuthorization::allowsAny([
            'workhub.transition.view',
            'workhub.transition.manage',
        ], IamAuthorization::resolveTenantFromRecord($transition), $user);
    }

    public function create(User $user): bool
    {
        return IamAuthorization::allows('workhub.transition.manage', null, $user);
    }

    public function update(User $user, Transition $transition): bool
    {
        return IamAuthorization::allows('workhub.transition.manage', IamAuthorization::resolveTenantFromRecord($transition), $user);
    }

    public function delete(User $user, Transition $transition): bool
    {
        return IamAuthorization::allows('workhub.transition.manage', IamAuthorization::resolveTenantFromRecord($transition), $user);
    }
}
