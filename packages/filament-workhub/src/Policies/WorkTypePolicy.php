<?php

namespace Haida\FilamentWorkhub\Policies;

use App\Models\User;
use Filamat\IamSuite\Support\IamAuthorization;
use Haida\FilamentWorkhub\Models\WorkType;

class WorkTypePolicy
{
    public function viewAny(User $user): bool
    {
        return IamAuthorization::allowsAny([
            'workhub.work_type.view',
            'workhub.work_type.manage',
        ], null, $user);
    }

    public function view(User $user, WorkType $workType): bool
    {
        return IamAuthorization::allowsAny([
            'workhub.work_type.view',
            'workhub.work_type.manage',
        ], IamAuthorization::resolveTenantFromRecord($workType), $user);
    }

    public function create(User $user): bool
    {
        return IamAuthorization::allows('workhub.work_type.manage', null, $user);
    }

    public function update(User $user, WorkType $workType): bool
    {
        return IamAuthorization::allows('workhub.work_type.manage', IamAuthorization::resolveTenantFromRecord($workType), $user);
    }

    public function delete(User $user, WorkType $workType): bool
    {
        return IamAuthorization::allows('workhub.work_type.manage', IamAuthorization::resolveTenantFromRecord($workType), $user);
    }
}
