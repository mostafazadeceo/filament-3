<?php

namespace Haida\FilamentCryptoCore\Policies;

use App\Models\User;
use Filamat\IamSuite\Support\IamAuthorization;
use Haida\FilamentCryptoCore\Models\CryptoNetworkFee;

class CryptoNetworkFeePolicy
{
    public function viewAny(User $user): bool
    {
        return IamAuthorization::allowsAny([
            'crypto.network_fees.view',
            'crypto.network_fees.manage',
        ], null, $user);
    }

    public function view(User $user, CryptoNetworkFee $record): bool
    {
        return IamAuthorization::allowsAny([
            'crypto.network_fees.view',
            'crypto.network_fees.manage',
        ], null, $user);
    }

    public function create(User $user): bool
    {
        return IamAuthorization::allows('crypto.network_fees.manage', null, $user);
    }

    public function update(User $user, CryptoNetworkFee $record): bool
    {
        return IamAuthorization::allows('crypto.network_fees.manage', null, $user);
    }

    public function delete(User $user, CryptoNetworkFee $record): bool
    {
        return IamAuthorization::allows('crypto.network_fees.manage', null, $user);
    }

    public function restore(User $user, CryptoNetworkFee $record): bool
    {
        return IamAuthorization::allows('crypto.network_fees.manage', null, $user);
    }

    public function forceDelete(User $user, CryptoNetworkFee $record): bool
    {
        return IamAuthorization::allows('crypto.network_fees.manage', null, $user);
    }
}
