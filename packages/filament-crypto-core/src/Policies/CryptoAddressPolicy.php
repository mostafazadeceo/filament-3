<?php

namespace Haida\FilamentCryptoCore\Policies;

use App\Models\User;
use Filamat\IamSuite\Support\IamAuthorization;
use Haida\FilamentCryptoCore\Models\CryptoAddress;

class CryptoAddressPolicy
{
    public function viewAny(User $user): bool
    {
        return IamAuthorization::allowsAny([
            'crypto.addresses.view',
            'crypto.addresses.manage',
        ], null, $user);
    }

    public function view(User $user, CryptoAddress $record): bool
    {
        return IamAuthorization::allowsAny([
            'crypto.addresses.view',
            'crypto.addresses.manage',
        ], IamAuthorization::resolveTenantFromRecord($record), $user);
    }

    public function create(User $user): bool
    {
        return IamAuthorization::allows('crypto.addresses.manage', null, $user);
    }

    public function update(User $user, CryptoAddress $record): bool
    {
        return IamAuthorization::allows('crypto.addresses.manage', IamAuthorization::resolveTenantFromRecord($record), $user);
    }

    public function delete(User $user, CryptoAddress $record): bool
    {
        return IamAuthorization::allows('crypto.addresses.manage', IamAuthorization::resolveTenantFromRecord($record), $user);
    }

    public function restore(User $user, CryptoAddress $record): bool
    {
        return IamAuthorization::allows('crypto.addresses.manage', IamAuthorization::resolveTenantFromRecord($record), $user);
    }

    public function forceDelete(User $user, CryptoAddress $record): bool
    {
        return IamAuthorization::allows('crypto.addresses.manage', IamAuthorization::resolveTenantFromRecord($record), $user);
    }
}
