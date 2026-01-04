<?php

namespace Haida\FilamentCommerceCore\Policies;

use App\Models\User;
use Filamat\IamSuite\Support\IamAuthorization;
use Haida\FilamentCommerceCore\Models\CommercePrice;

class CommercePricePolicy
{
    public function viewAny(User $user): bool
    {
        return IamAuthorization::allowsAny([
            'commerce.pricing.view',
            'commerce.pricing.manage',
        ], null, $user);
    }

    public function view(User $user, CommercePrice $record): bool
    {
        return IamAuthorization::allowsAny([
            'commerce.pricing.view',
            'commerce.pricing.manage',
        ], IamAuthorization::resolveTenantFromRecord($record), $user);
    }

    public function create(User $user): bool
    {
        return IamAuthorization::allows('commerce.pricing.manage', null, $user);
    }

    public function update(User $user, CommercePrice $record): bool
    {
        return IamAuthorization::allows('commerce.pricing.manage', IamAuthorization::resolveTenantFromRecord($record), $user);
    }

    public function delete(User $user, CommercePrice $record): bool
    {
        return IamAuthorization::allows('commerce.pricing.manage', IamAuthorization::resolveTenantFromRecord($record), $user);
    }

    public function restore(User $user, CommercePrice $record): bool
    {
        return IamAuthorization::allows('commerce.pricing.manage', IamAuthorization::resolveTenantFromRecord($record), $user);
    }

    public function forceDelete(User $user, CommercePrice $record): bool
    {
        return IamAuthorization::allows('commerce.pricing.manage', IamAuthorization::resolveTenantFromRecord($record), $user);
    }
}
