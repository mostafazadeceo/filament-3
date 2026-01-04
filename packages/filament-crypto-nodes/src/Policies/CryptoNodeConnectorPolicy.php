<?php

namespace Haida\FilamentCryptoNodes\Policies;

use App\Models\User;
use Filamat\IamSuite\Support\IamAuthorization;
use Haida\FilamentCryptoNodes\Models\CryptoNodeConnector;

class CryptoNodeConnectorPolicy
{
    public function viewAny(User $user): bool
    {
        return IamAuthorization::allowsAny([
            'crypto.nodes.view',
            'crypto.nodes.manage',
        ], null, $user);
    }

    public function view(User $user, CryptoNodeConnector $record): bool
    {
        return IamAuthorization::allowsAny([
            'crypto.nodes.view',
            'crypto.nodes.manage',
        ], IamAuthorization::resolveTenantFromRecord($record), $user);
    }

    public function create(User $user): bool
    {
        return IamAuthorization::allows('crypto.nodes.manage', null, $user);
    }

    public function update(User $user, CryptoNodeConnector $record): bool
    {
        return IamAuthorization::allows('crypto.nodes.manage', IamAuthorization::resolveTenantFromRecord($record), $user);
    }

    public function delete(User $user, CryptoNodeConnector $record): bool
    {
        return IamAuthorization::allows('crypto.nodes.manage', IamAuthorization::resolveTenantFromRecord($record), $user);
    }

    public function restore(User $user, CryptoNodeConnector $record): bool
    {
        return IamAuthorization::allows('crypto.nodes.manage', IamAuthorization::resolveTenantFromRecord($record), $user);
    }

    public function forceDelete(User $user, CryptoNodeConnector $record): bool
    {
        return IamAuthorization::allows('crypto.nodes.manage', IamAuthorization::resolveTenantFromRecord($record), $user);
    }
}
