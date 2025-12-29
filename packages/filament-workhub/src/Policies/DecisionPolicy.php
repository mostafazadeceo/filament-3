<?php

namespace Haida\FilamentWorkhub\Policies;

use App\Models\User;
use Filamat\IamSuite\Support\IamAuthorization;
use Haida\FilamentWorkhub\Models\Decision;

class DecisionPolicy
{
    public function viewAny(User $user): bool
    {
        return IamAuthorization::allowsAny([
            'workhub.decision.view',
            'workhub.decision.manage',
        ], null, $user);
    }

    public function view(User $user, Decision $decision): bool
    {
        return IamAuthorization::allowsAny([
            'workhub.decision.view',
            'workhub.decision.manage',
        ], IamAuthorization::resolveTenantFromRecord($decision), $user);
    }

    public function create(User $user): bool
    {
        return IamAuthorization::allows('workhub.decision.manage', null, $user);
    }

    public function delete(User $user, Decision $decision): bool
    {
        return IamAuthorization::allows('workhub.decision.manage', IamAuthorization::resolveTenantFromRecord($decision), $user);
    }
}
