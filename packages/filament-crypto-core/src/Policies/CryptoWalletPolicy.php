<?php

namespace Haida\FilamentCryptoCore\Policies;

use App\Models\User;
use Filamat\IamSuite\Support\IamAuthorization;
use Haida\FilamentCryptoCore\Models\CryptoWallet;

class CryptoWalletPolicy
{
    public function viewAny(User $user): bool
    {
        return IamAuthorization::allowsAny([
            'crypto.wallets.view',
            'crypto.wallets.manage',
        ], null, $user);
    }

    public function view(User $user, CryptoWallet $record): bool
    {
        return IamAuthorization::allowsAny([
            'crypto.wallets.view',
            'crypto.wallets.manage',
        ], IamAuthorization::resolveTenantFromRecord($record), $user);
    }

    public function create(User $user): bool
    {
        return IamAuthorization::allows('crypto.wallets.manage', null, $user);
    }

    public function update(User $user, CryptoWallet $record): bool
    {
        return IamAuthorization::allows('crypto.wallets.manage', IamAuthorization::resolveTenantFromRecord($record), $user);
    }

    public function delete(User $user, CryptoWallet $record): bool
    {
        return IamAuthorization::allows('crypto.wallets.manage', IamAuthorization::resolveTenantFromRecord($record), $user);
    }

    public function restore(User $user, CryptoWallet $record): bool
    {
        return IamAuthorization::allows('crypto.wallets.manage', IamAuthorization::resolveTenantFromRecord($record), $user);
    }

    public function forceDelete(User $user, CryptoWallet $record): bool
    {
        return IamAuthorization::allows('crypto.wallets.manage', IamAuthorization::resolveTenantFromRecord($record), $user);
    }
}
