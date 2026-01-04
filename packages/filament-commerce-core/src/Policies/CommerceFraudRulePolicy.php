<?php

namespace Haida\FilamentCommerceCore\Policies;

use App\Models\User;
use Filamat\IamSuite\Support\IamAuthorization;
use Haida\FilamentCommerceCore\Models\CommerceFraudRule;

class CommerceFraudRulePolicy
{
    public function viewAny(User $user): bool
    {
        return IamAuthorization::allowsAny(['commerce.compliance.view', 'commerce.compliance.manage'], null, $user);
    }

    public function view(User $user, CommerceFraudRule $record): bool
    {
        return IamAuthorization::allowsAny(['commerce.compliance.view', 'commerce.compliance.manage'], IamAuthorization::resolveTenantFromRecord($record), $user);
    }

    public function create(User $user): bool
    {
        return IamAuthorization::allows('commerce.compliance.manage', null, $user);
    }

    public function update(User $user, CommerceFraudRule $record): bool
    {
        return IamAuthorization::allows('commerce.compliance.manage', IamAuthorization::resolveTenantFromRecord($record), $user);
    }

    public function delete(User $user, CommerceFraudRule $record): bool
    {
        return IamAuthorization::allows('commerce.compliance.manage', IamAuthorization::resolveTenantFromRecord($record), $user);
    }
}
