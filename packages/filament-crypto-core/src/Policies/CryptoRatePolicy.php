<?php

namespace Haida\FilamentCryptoCore\Policies;

use App\Models\User;
use Filamat\IamSuite\Support\IamAuthorization;
use Haida\FilamentCryptoCore\Models\CryptoRate;

class CryptoRatePolicy
{
    public function viewAny(User $user): bool
    {
        return IamAuthorization::allowsAny([
            'crypto.rates.view',
            'crypto.rates.manage',
        ], null, $user);
    }

    public function view(User $user, CryptoRate $record): bool
    {
        return IamAuthorization::allowsAny([
            'crypto.rates.view',
            'crypto.rates.manage',
        ], null, $user);
    }

    public function create(User $user): bool
    {
        return IamAuthorization::allows('crypto.rates.manage', null, $user);
    }

    public function update(User $user, CryptoRate $record): bool
    {
        return IamAuthorization::allows('crypto.rates.manage', null, $user);
    }

    public function delete(User $user, CryptoRate $record): bool
    {
        return IamAuthorization::allows('crypto.rates.manage', null, $user);
    }

    public function restore(User $user, CryptoRate $record): bool
    {
        return IamAuthorization::allows('crypto.rates.manage', null, $user);
    }

    public function forceDelete(User $user, CryptoRate $record): bool
    {
        return IamAuthorization::allows('crypto.rates.manage', null, $user);
    }
}
