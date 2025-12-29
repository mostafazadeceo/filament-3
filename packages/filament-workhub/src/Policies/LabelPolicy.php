<?php

namespace Haida\FilamentWorkhub\Policies;

use App\Models\User;
use Filamat\IamSuite\Support\IamAuthorization;
use Haida\FilamentWorkhub\Models\Label;

class LabelPolicy
{
    public function viewAny(User $user): bool
    {
        return IamAuthorization::allowsAny([
            'workhub.label.view',
            'workhub.label.manage',
        ], null, $user);
    }

    public function view(User $user, Label $label): bool
    {
        return IamAuthorization::allowsAny([
            'workhub.label.view',
            'workhub.label.manage',
        ], IamAuthorization::resolveTenantFromRecord($label), $user);
    }

    public function create(User $user): bool
    {
        return IamAuthorization::allows('workhub.label.manage', null, $user);
    }

    public function delete(User $user, Label $label): bool
    {
        return IamAuthorization::allows('workhub.label.manage', IamAuthorization::resolveTenantFromRecord($label), $user);
    }
}
