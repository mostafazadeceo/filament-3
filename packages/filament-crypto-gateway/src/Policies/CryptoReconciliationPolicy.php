<?php

namespace Haida\FilamentCryptoGateway\Policies;

use App\Models\User;
use Filamat\IamSuite\Support\IamAuthorization;
use Haida\FilamentCryptoGateway\Models\CryptoReconciliation;

class CryptoReconciliationPolicy
{
    public function viewAny(User $user): bool
    {
        return IamAuthorization::allowsAny([
            'crypto.reconciliations.view',
            'crypto.reconciliations.manage',
        ], null, $user);
    }

    public function view(User $user, CryptoReconciliation $record): bool
    {
        return IamAuthorization::allowsAny([
            'crypto.reconciliations.view',
            'crypto.reconciliations.manage',
        ], IamAuthorization::resolveTenantFromRecord($record), $user);
    }

    public function create(User $user): bool
    {
        return IamAuthorization::allowsAny([
            'crypto.reconciliations.manage',
            'crypto.reconcile.run',
        ], null, $user);
    }

    public function update(User $user, CryptoReconciliation $record): bool
    {
        return IamAuthorization::allows('crypto.reconciliations.manage', IamAuthorization::resolveTenantFromRecord($record), $user);
    }

    public function delete(User $user, CryptoReconciliation $record): bool
    {
        return IamAuthorization::allows('crypto.reconciliations.manage', IamAuthorization::resolveTenantFromRecord($record), $user);
    }

    public function restore(User $user, CryptoReconciliation $record): bool
    {
        return IamAuthorization::allows('crypto.reconciliations.manage', IamAuthorization::resolveTenantFromRecord($record), $user);
    }

    public function forceDelete(User $user, CryptoReconciliation $record): bool
    {
        return IamAuthorization::allows('crypto.reconciliations.manage', IamAuthorization::resolveTenantFromRecord($record), $user);
    }
}
