<?php

namespace Haida\FilamentWorkhub\Policies;

use App\Models\User;
use Filamat\IamSuite\Support\IamAuthorization;
use Haida\FilamentWorkhub\Models\Workflow;

class WorkflowPolicy
{
    public function viewAny(User $user): bool
    {
        return IamAuthorization::allowsAny([
            'workhub.workflow.view',
            'workhub.workflow.manage',
        ], null, $user);
    }

    public function view(User $user, Workflow $workflow): bool
    {
        return IamAuthorization::allowsAny([
            'workhub.workflow.view',
            'workhub.workflow.manage',
        ], IamAuthorization::resolveTenantFromRecord($workflow), $user);
    }

    public function create(User $user): bool
    {
        return IamAuthorization::allows('workhub.workflow.manage', null, $user);
    }

    public function update(User $user, Workflow $workflow): bool
    {
        return IamAuthorization::allows('workhub.workflow.manage', IamAuthorization::resolveTenantFromRecord($workflow), $user);
    }

    public function delete(User $user, Workflow $workflow): bool
    {
        return IamAuthorization::allows('workhub.workflow.manage', IamAuthorization::resolveTenantFromRecord($workflow), $user);
    }
}
