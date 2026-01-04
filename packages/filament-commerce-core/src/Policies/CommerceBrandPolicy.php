<?php

namespace Haida\FilamentCommerceCore\Policies;

use App\Models\User;
use Filamat\IamSuite\Support\IamAuthorization;
use Haida\FilamentCommerceCore\Models\CommerceBrand;

class CommerceBrandPolicy
{
    public function viewAny(User $user): bool
    {
        return IamAuthorization::allowsAny([
            'commerce.catalog.view',
            'commerce.catalog.manage',
        ], null, $user);
    }

    public function view(User $user, CommerceBrand $record): bool
    {
        return IamAuthorization::allowsAny([
            'commerce.catalog.view',
            'commerce.catalog.manage',
        ], IamAuthorization::resolveTenantFromRecord($record), $user);
    }

    public function create(User $user): bool
    {
        return IamAuthorization::allows('commerce.catalog.manage', null, $user);
    }

    public function update(User $user, CommerceBrand $record): bool
    {
        return IamAuthorization::allows('commerce.catalog.manage', IamAuthorization::resolveTenantFromRecord($record), $user);
    }

    public function delete(User $user, CommerceBrand $record): bool
    {
        return IamAuthorization::allows('commerce.catalog.manage', IamAuthorization::resolveTenantFromRecord($record), $user);
    }

    public function restore(User $user, CommerceBrand $record): bool
    {
        return IamAuthorization::allows('commerce.catalog.manage', IamAuthorization::resolveTenantFromRecord($record), $user);
    }

    public function forceDelete(User $user, CommerceBrand $record): bool
    {
        return IamAuthorization::allows('commerce.catalog.manage', IamAuthorization::resolveTenantFromRecord($record), $user);
    }
}
