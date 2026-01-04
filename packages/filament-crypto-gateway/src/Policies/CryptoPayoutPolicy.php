<?php

namespace Haida\FilamentCryptoGateway\Policies;

use App\Models\User;
use Filamat\IamSuite\Support\IamAuthorization;
use Haida\FilamentCryptoGateway\Models\CryptoPayout;

class CryptoPayoutPolicy
{
    public function viewAny(User $user): bool
    {
        return IamAuthorization::allowsAny([
            'crypto.payouts.view',
            'crypto.payouts.manage',
        ], null, $user);
    }

    public function view(User $user, CryptoPayout $record): bool
    {
        return IamAuthorization::allowsAny([
            'crypto.payouts.view',
            'crypto.payouts.manage',
        ], IamAuthorization::resolveTenantFromRecord($record), $user);
    }

    public function create(User $user): bool
    {
        return IamAuthorization::allows('crypto.payouts.manage', null, $user);
    }

    public function update(User $user, CryptoPayout $record): bool
    {
        return IamAuthorization::allows('crypto.payouts.manage', IamAuthorization::resolveTenantFromRecord($record), $user);
    }

    public function approve(User $user, CryptoPayout $record): bool
    {
        return IamAuthorization::allowsAny([
            'crypto.payouts.approve',
            'crypto.payouts.manage',
        ], IamAuthorization::resolveTenantFromRecord($record), $user);
    }

    public function delete(User $user, CryptoPayout $record): bool
    {
        return IamAuthorization::allows('crypto.payouts.manage', IamAuthorization::resolveTenantFromRecord($record), $user);
    }

    public function restore(User $user, CryptoPayout $record): bool
    {
        return IamAuthorization::allows('crypto.payouts.manage', IamAuthorization::resolveTenantFromRecord($record), $user);
    }

    public function forceDelete(User $user, CryptoPayout $record): bool
    {
        return IamAuthorization::allows('crypto.payouts.manage', IamAuthorization::resolveTenantFromRecord($record), $user);
    }
}
