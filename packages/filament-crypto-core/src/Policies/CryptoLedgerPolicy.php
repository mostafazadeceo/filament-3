<?php

namespace Haida\FilamentCryptoCore\Policies;

use App\Models\User;
use Filamat\IamSuite\Support\IamAuthorization;
use Haida\FilamentCryptoCore\Models\CryptoLedger;

class CryptoLedgerPolicy
{
    public function viewAny(User $user): bool
    {
        return IamAuthorization::allowsAny([
            'crypto.ledgers.view',
            'crypto.ledgers.manage',
        ], null, $user);
    }

    public function view(User $user, CryptoLedger $record): bool
    {
        return IamAuthorization::allowsAny([
            'crypto.ledgers.view',
            'crypto.ledgers.manage',
        ], IamAuthorization::resolveTenantFromRecord($record), $user);
    }

    public function create(User $user): bool
    {
        return IamAuthorization::allows('crypto.ledgers.manage', null, $user);
    }

    public function update(User $user, CryptoLedger $record): bool
    {
        return IamAuthorization::allows('crypto.ledgers.manage', IamAuthorization::resolveTenantFromRecord($record), $user);
    }

    public function delete(User $user, CryptoLedger $record): bool
    {
        return IamAuthorization::allows('crypto.ledgers.manage', IamAuthorization::resolveTenantFromRecord($record), $user);
    }

    public function restore(User $user, CryptoLedger $record): bool
    {
        return IamAuthorization::allows('crypto.ledgers.manage', IamAuthorization::resolveTenantFromRecord($record), $user);
    }

    public function forceDelete(User $user, CryptoLedger $record): bool
    {
        return IamAuthorization::allows('crypto.ledgers.manage', IamAuthorization::resolveTenantFromRecord($record), $user);
    }
}
