<?php

namespace Haida\FilamentWorkhub\Policies;

use App\Models\User;
use Filamat\IamSuite\Support\IamAuthorization;
use Haida\FilamentWorkhub\Models\AutomationRule;

class AutomationRulePolicy
{
    public function viewAny(User $user): bool
    {
        return IamAuthorization::allowsAny([
            'workhub.automation.view',
            'workhub.automation.manage',
        ], null, $user);
    }

    public function view(User $user, AutomationRule $rule): bool
    {
        return IamAuthorization::allowsAny([
            'workhub.automation.view',
            'workhub.automation.manage',
        ], IamAuthorization::resolveTenantFromRecord($rule), $user);
    }

    public function create(User $user): bool
    {
        return IamAuthorization::allows('workhub.automation.manage', null, $user);
    }

    public function update(User $user, AutomationRule $rule): bool
    {
        return IamAuthorization::allows('workhub.automation.manage', IamAuthorization::resolveTenantFromRecord($rule), $user);
    }

    public function delete(User $user, AutomationRule $rule): bool
    {
        return IamAuthorization::allows('workhub.automation.manage', IamAuthorization::resolveTenantFromRecord($rule), $user);
    }
}
