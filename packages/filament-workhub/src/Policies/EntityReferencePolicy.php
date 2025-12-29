<?php

namespace Haida\FilamentWorkhub\Policies;

use App\Models\User;
use Filamat\IamSuite\Support\IamAuthorization;
use Haida\FilamentWorkhub\Models\EntityReference;

class EntityReferencePolicy
{
    public function viewAny(User $user): bool
    {
        return IamAuthorization::allowsAny([
            'workhub.link.view',
            'workhub.link.manage',
        ], null, $user);
    }

    public function view(User $user, EntityReference $reference): bool
    {
        return IamAuthorization::allowsAny([
            'workhub.link.view',
            'workhub.link.manage',
        ], IamAuthorization::resolveTenantFromRecord($reference), $user);
    }

    public function create(User $user): bool
    {
        return IamAuthorization::allows('workhub.link.manage', null, $user);
    }

    public function delete(User $user, EntityReference $reference): bool
    {
        return IamAuthorization::allows('workhub.link.manage', IamAuthorization::resolveTenantFromRecord($reference), $user);
    }
}
