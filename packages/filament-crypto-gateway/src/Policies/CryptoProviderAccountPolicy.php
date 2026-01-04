<?php

namespace Haida\FilamentCryptoGateway\Policies;

use App\Models\User;
use Filamat\IamSuite\Support\IamAuthorization;
use Haida\FilamentCryptoGateway\Models\CryptoProviderAccount;

class CryptoProviderAccountPolicy
{
    public function viewAny(User $user): bool
    {
        return IamAuthorization::allowsAny([
            'crypto.providers.view',
            'crypto.providers.manage',
        ], null, $user);
    }

    public function view(User $user, CryptoProviderAccount $record): bool
    {
        return IamAuthorization::allowsAny([
            'crypto.providers.view',
            'crypto.providers.manage',
        ], IamAuthorization::resolveTenantFromRecord($record), $user);
    }

    public function create(User $user): bool
    {
        return IamAuthorization::allows('crypto.providers.manage', null, $user);
    }

    public function update(User $user, CryptoProviderAccount $record): bool
    {
        return IamAuthorization::allows('crypto.providers.manage', IamAuthorization::resolveTenantFromRecord($record), $user);
    }

    public function delete(User $user, CryptoProviderAccount $record): bool
    {
        return IamAuthorization::allows('crypto.providers.manage', IamAuthorization::resolveTenantFromRecord($record), $user);
    }

    public function restore(User $user, CryptoProviderAccount $record): bool
    {
        return IamAuthorization::allows('crypto.providers.manage', IamAuthorization::resolveTenantFromRecord($record), $user);
    }

    public function forceDelete(User $user, CryptoProviderAccount $record): bool
    {
        return IamAuthorization::allows('crypto.providers.manage', IamAuthorization::resolveTenantFromRecord($record), $user);
    }
}
