<?php

namespace Haida\FilamentCryptoCore\Policies;

use App\Models\User;
use Filamat\IamSuite\Support\IamAuthorization;
use Haida\FilamentCryptoCore\Models\CryptoFeePolicy;

class CryptoFeePolicyPolicy
{
    public function viewAny(User $user): bool
    {
        return IamAuthorization::allowsAny([
            'crypto.fee_policies.view',
            'crypto.fee_policies.manage',
        ], null, $user);
    }

    public function view(User $user, CryptoFeePolicy $record): bool
    {
        return IamAuthorization::allowsAny([
            'crypto.fee_policies.view',
            'crypto.fee_policies.manage',
        ], IamAuthorization::resolveTenantFromRecord($record), $user);
    }

    public function create(User $user): bool
    {
        return IamAuthorization::allows('crypto.fee_policies.manage', null, $user);
    }

    public function update(User $user, CryptoFeePolicy $record): bool
    {
        return IamAuthorization::allows('crypto.fee_policies.manage', IamAuthorization::resolveTenantFromRecord($record), $user);
    }

    public function delete(User $user, CryptoFeePolicy $record): bool
    {
        return IamAuthorization::allows('crypto.fee_policies.manage', IamAuthorization::resolveTenantFromRecord($record), $user);
    }

    public function restore(User $user, CryptoFeePolicy $record): bool
    {
        return IamAuthorization::allows('crypto.fee_policies.manage', IamAuthorization::resolveTenantFromRecord($record), $user);
    }

    public function forceDelete(User $user, CryptoFeePolicy $record): bool
    {
        return IamAuthorization::allows('crypto.fee_policies.manage', IamAuthorization::resolveTenantFromRecord($record), $user);
    }
}
