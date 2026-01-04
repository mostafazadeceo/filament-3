<?php

namespace Haida\FilamentCommerceCore\Policies;

use App\Models\User;
use Filamat\IamSuite\Support\IamAuthorization;
use Haida\FilamentCommerceCore\Models\CommerceCustomer;

class CommerceCustomerPolicy
{
    public function viewAny(User $user): bool
    {
        return IamAuthorization::allowsAny([
            'commerce.customers.view',
            'commerce.customers.manage',
        ], null, $user);
    }

    public function view(User $user, CommerceCustomer $record): bool
    {
        return IamAuthorization::allowsAny([
            'commerce.customers.view',
            'commerce.customers.manage',
        ], IamAuthorization::resolveTenantFromRecord($record), $user);
    }

    public function create(User $user): bool
    {
        return IamAuthorization::allows('commerce.customers.manage', null, $user);
    }

    public function update(User $user, CommerceCustomer $record): bool
    {
        return IamAuthorization::allows('commerce.customers.manage', IamAuthorization::resolveTenantFromRecord($record), $user);
    }

    public function delete(User $user, CommerceCustomer $record): bool
    {
        return IamAuthorization::allows('commerce.customers.manage', IamAuthorization::resolveTenantFromRecord($record), $user);
    }

    public function restore(User $user, CommerceCustomer $record): bool
    {
        return IamAuthorization::allows('commerce.customers.manage', IamAuthorization::resolveTenantFromRecord($record), $user);
    }

    public function forceDelete(User $user, CommerceCustomer $record): bool
    {
        return IamAuthorization::allows('commerce.customers.manage', IamAuthorization::resolveTenantFromRecord($record), $user);
    }
}
