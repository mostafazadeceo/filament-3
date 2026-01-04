<?php

namespace Haida\FilamentCommerceCore\Policies;

use App\Models\User;
use Filamat\IamSuite\Support\IamAuthorization;
use Haida\FilamentCommerceCore\Models\CommerceException;

class CommerceExceptionPolicy
{
    public function viewAny(User $user): bool
    {
        return IamAuthorization::allowsAny(['commerce.compliance.view', 'commerce.compliance.manage'], null, $user);
    }

    public function view(User $user, CommerceException $record): bool
    {
        return IamAuthorization::allowsAny(['commerce.compliance.view', 'commerce.compliance.manage'], IamAuthorization::resolveTenantFromRecord($record), $user);
    }

    public function create(User $user): bool
    {
        return IamAuthorization::allows('commerce.compliance.manage', null, $user);
    }

    public function update(User $user, CommerceException $record): bool
    {
        return IamAuthorization::allows('commerce.compliance.manage', IamAuthorization::resolveTenantFromRecord($record), $user);
    }

    public function delete(User $user, CommerceException $record): bool
    {
        return IamAuthorization::allows('commerce.compliance.manage', IamAuthorization::resolveTenantFromRecord($record), $user);
    }

    public function resolve(User $user, CommerceException $record): bool
    {
        return IamAuthorization::allows('commerce.compliance.resolve', IamAuthorization::resolveTenantFromRecord($record), $user);
    }
}
