<?php

namespace Haida\FilamentWorkhub\Policies;

use App\Models\User;
use Filamat\IamSuite\Support\IamAuthorization;
use Haida\FilamentWorkhub\Models\Project;

class ProjectPolicy
{
    public function viewAny(User $user): bool
    {
        return IamAuthorization::allowsAny([
            'workhub.project.view',
            'workhub.project.manage',
        ], null, $user);
    }

    public function view(User $user, Project $project): bool
    {
        return IamAuthorization::allowsAny([
            'workhub.project.view',
            'workhub.project.manage',
        ], IamAuthorization::resolveTenantFromRecord($project), $user);
    }

    public function create(User $user): bool
    {
        return IamAuthorization::allows('workhub.project.manage', null, $user);
    }

    public function update(User $user, Project $project): bool
    {
        return IamAuthorization::allows('workhub.project.manage', IamAuthorization::resolveTenantFromRecord($project), $user);
    }

    public function delete(User $user, Project $project): bool
    {
        return IamAuthorization::allows('workhub.project.manage', IamAuthorization::resolveTenantFromRecord($project), $user);
    }

    public function restore(User $user, Project $project): bool
    {
        return IamAuthorization::allows('workhub.project.manage', IamAuthorization::resolveTenantFromRecord($project), $user);
    }

    public function forceDelete(User $user, Project $project): bool
    {
        return IamAuthorization::allows('workhub.project.manage', IamAuthorization::resolveTenantFromRecord($project), $user);
    }
}
