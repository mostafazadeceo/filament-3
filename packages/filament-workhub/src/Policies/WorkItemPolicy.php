<?php

namespace Haida\FilamentWorkhub\Policies;

use App\Models\User;
use Filamat\IamSuite\Support\IamAuthorization;
use Haida\FilamentWorkhub\Models\WorkItem;

class WorkItemPolicy
{
    public function viewAny(User $user): bool
    {
        return IamAuthorization::allowsAny([
            'workhub.work_item.view',
            'workhub.work_item.manage',
        ], null, $user);
    }

    public function view(User $user, WorkItem $workItem): bool
    {
        return IamAuthorization::allowsAny([
            'workhub.work_item.view',
            'workhub.work_item.manage',
        ], IamAuthorization::resolveTenantFromRecord($workItem), $user);
    }

    public function create(User $user): bool
    {
        return IamAuthorization::allows('workhub.work_item.manage', null, $user);
    }

    public function update(User $user, WorkItem $workItem): bool
    {
        return IamAuthorization::allows('workhub.work_item.manage', IamAuthorization::resolveTenantFromRecord($workItem), $user);
    }

    public function delete(User $user, WorkItem $workItem): bool
    {
        return IamAuthorization::allows('workhub.work_item.manage', IamAuthorization::resolveTenantFromRecord($workItem), $user);
    }

    public function restore(User $user, WorkItem $workItem): bool
    {
        return IamAuthorization::allows('workhub.work_item.manage', IamAuthorization::resolveTenantFromRecord($workItem), $user);
    }

    public function forceDelete(User $user, WorkItem $workItem): bool
    {
        return IamAuthorization::allows('workhub.work_item.manage', IamAuthorization::resolveTenantFromRecord($workItem), $user);
    }
}
