<?php

namespace Haida\FilamentCommerceCore\Policies;

use App\Models\User;
use Filamat\IamSuite\Support\IamAuthorization;
use Haida\FilamentCommerceCore\Models\CommerceInventoryItem;

class CommerceInventoryItemPolicy
{
    public function viewAny(User $user): bool
    {
        return IamAuthorization::allowsAny([
            'commerce.inventory.view',
            'commerce.inventory.manage',
        ], null, $user);
    }

    public function view(User $user, CommerceInventoryItem $record): bool
    {
        return IamAuthorization::allowsAny([
            'commerce.inventory.view',
            'commerce.inventory.manage',
        ], IamAuthorization::resolveTenantFromRecord($record), $user);
    }

    public function create(User $user): bool
    {
        return IamAuthorization::allows('commerce.inventory.manage', null, $user);
    }

    public function update(User $user, CommerceInventoryItem $record): bool
    {
        return IamAuthorization::allows('commerce.inventory.manage', IamAuthorization::resolveTenantFromRecord($record), $user);
    }

    public function delete(User $user, CommerceInventoryItem $record): bool
    {
        return IamAuthorization::allows('commerce.inventory.manage', IamAuthorization::resolveTenantFromRecord($record), $user);
    }

    public function restore(User $user, CommerceInventoryItem $record): bool
    {
        return IamAuthorization::allows('commerce.inventory.manage', IamAuthorization::resolveTenantFromRecord($record), $user);
    }

    public function forceDelete(User $user, CommerceInventoryItem $record): bool
    {
        return IamAuthorization::allows('commerce.inventory.manage', IamAuthorization::resolveTenantFromRecord($record), $user);
    }
}
