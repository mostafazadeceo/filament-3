<?php

namespace Haida\FilamentCryptoCore\Policies;

use App\Models\User;
use Filamat\IamSuite\Support\IamAuthorization;
use Haida\FilamentCryptoCore\Models\CryptoAccount;

class CryptoAccountPolicy
{
    public function viewAny(User $user): bool
    {
        return IamAuthorization::allowsAny([
            'crypto.accounts.view',
            'crypto.accounts.manage',
        ], null, $user);
    }

    public function view(User $user, CryptoAccount $record): bool
    {
        return IamAuthorization::allowsAny([
            'crypto.accounts.view',
            'crypto.accounts.manage',
        ], IamAuthorization::resolveTenantFromRecord($record), $user);
    }

    public function create(User $user): bool
    {
        return IamAuthorization::allows('crypto.accounts.manage', null, $user);
    }

    public function update(User $user, CryptoAccount $record): bool
    {
        return IamAuthorization::allows('crypto.accounts.manage', IamAuthorization::resolveTenantFromRecord($record), $user);
    }

    public function delete(User $user, CryptoAccount $record): bool
    {
        return IamAuthorization::allows('crypto.accounts.manage', IamAuthorization::resolveTenantFromRecord($record), $user);
    }

    public function restore(User $user, CryptoAccount $record): bool
    {
        return IamAuthorization::allows('crypto.accounts.manage', IamAuthorization::resolveTenantFromRecord($record), $user);
    }

    public function forceDelete(User $user, CryptoAccount $record): bool
    {
        return IamAuthorization::allows('crypto.accounts.manage', IamAuthorization::resolveTenantFromRecord($record), $user);
    }
}
