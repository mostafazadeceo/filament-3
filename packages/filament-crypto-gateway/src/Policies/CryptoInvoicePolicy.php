<?php

namespace Haida\FilamentCryptoGateway\Policies;

use App\Models\User;
use Filamat\IamSuite\Support\IamAuthorization;
use Haida\FilamentCryptoGateway\Models\CryptoInvoice;

class CryptoInvoicePolicy
{
    public function viewAny(User $user): bool
    {
        return IamAuthorization::allowsAny([
            'crypto.invoices.view',
            'crypto.invoices.manage',
        ], null, $user);
    }

    public function view(User $user, CryptoInvoice $record): bool
    {
        return IamAuthorization::allowsAny([
            'crypto.invoices.view',
            'crypto.invoices.manage',
        ], IamAuthorization::resolveTenantFromRecord($record), $user);
    }

    public function create(User $user): bool
    {
        return IamAuthorization::allows('crypto.invoices.manage', null, $user);
    }

    public function update(User $user, CryptoInvoice $record): bool
    {
        return IamAuthorization::allows('crypto.invoices.manage', IamAuthorization::resolveTenantFromRecord($record), $user);
    }

    public function delete(User $user, CryptoInvoice $record): bool
    {
        return IamAuthorization::allows('crypto.invoices.manage', IamAuthorization::resolveTenantFromRecord($record), $user);
    }

    public function restore(User $user, CryptoInvoice $record): bool
    {
        return IamAuthorization::allows('crypto.invoices.manage', IamAuthorization::resolveTenantFromRecord($record), $user);
    }

    public function forceDelete(User $user, CryptoInvoice $record): bool
    {
        return IamAuthorization::allows('crypto.invoices.manage', IamAuthorization::resolveTenantFromRecord($record), $user);
    }
}
