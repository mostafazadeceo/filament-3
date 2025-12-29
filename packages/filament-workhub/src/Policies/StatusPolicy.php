<?php

namespace Haida\FilamentWorkhub\Policies;

use App\Models\User;
use Filamat\IamSuite\Support\IamAuthorization;
use Haida\FilamentWorkhub\Models\Status;

class StatusPolicy
{
    public function viewAny(User $user): bool
    {
        return IamAuthorization::allowsAny([
            'workhub.status.view',
            'workhub.status.manage',
        ], null, $user);
    }

    public function view(User $user, Status $status): bool
    {
        return IamAuthorization::allowsAny([
            'workhub.status.view',
            'workhub.status.manage',
        ], IamAuthorization::resolveTenantFromRecord($status), $user);
    }

    public function create(User $user): bool
    {
        return IamAuthorization::allows('workhub.status.manage', null, $user);
    }

    public function update(User $user, Status $status): bool
    {
        return IamAuthorization::allows('workhub.status.manage', IamAuthorization::resolveTenantFromRecord($status), $user);
    }

    public function delete(User $user, Status $status): bool
    {
        return IamAuthorization::allows('workhub.status.manage', IamAuthorization::resolveTenantFromRecord($status), $user);
    }
}
