<?php

namespace Vendor\FilamentAccountingIr\Policies;

use Vendor\FilamentAccountingIr\Models\Project;
use Vendor\FilamentAccountingIr\Policies\Concerns\HandlesAccountingPermissions;

class ProjectPolicy
{
    use HandlesAccountingPermissions;

    public function viewAny(): bool
    {
        return $this->allow('accounting.project.view');
    }

    public function view(Project $project): bool
    {
        return $this->allow('accounting.project.view', $project);
    }

    public function create(): bool
    {
        return $this->allow('accounting.project.manage');
    }

    public function update(Project $project): bool
    {
        return $this->allow('accounting.project.manage', $project);
    }

    public function delete(Project $project): bool
    {
        return $this->allow('accounting.project.manage', $project);
    }
}
