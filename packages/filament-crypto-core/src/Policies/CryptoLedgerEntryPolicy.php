<?php

namespace Haida\FilamentCryptoCore\Policies;

use App\Models\User;
use Filamat\IamSuite\Support\IamAuthorization;
use Haida\FilamentCryptoCore\Models\CryptoLedgerEntry;

class CryptoLedgerEntryPolicy
{
    public function viewAny(User $user): bool
    {
        return IamAuthorization::allowsAny([
            'crypto.ledger_entries.view',
            'crypto.ledger_entries.manage',
        ], null, $user);
    }

    public function view(User $user, CryptoLedgerEntry $record): bool
    {
        return IamAuthorization::allowsAny([
            'crypto.ledger_entries.view',
            'crypto.ledger_entries.manage',
        ], IamAuthorization::resolveTenantFromRecord($record), $user);
    }

    public function create(User $user): bool
    {
        return IamAuthorization::allows('crypto.ledger_entries.manage', null, $user);
    }

    public function update(User $user, CryptoLedgerEntry $record): bool
    {
        return IamAuthorization::allows('crypto.ledger_entries.manage', IamAuthorization::resolveTenantFromRecord($record), $user);
    }

    public function delete(User $user, CryptoLedgerEntry $record): bool
    {
        return IamAuthorization::allows('crypto.ledger_entries.manage', IamAuthorization::resolveTenantFromRecord($record), $user);
    }

    public function restore(User $user, CryptoLedgerEntry $record): bool
    {
        return IamAuthorization::allows('crypto.ledger_entries.manage', IamAuthorization::resolveTenantFromRecord($record), $user);
    }

    public function forceDelete(User $user, CryptoLedgerEntry $record): bool
    {
        return IamAuthorization::allows('crypto.ledger_entries.manage', IamAuthorization::resolveTenantFromRecord($record), $user);
    }
}
