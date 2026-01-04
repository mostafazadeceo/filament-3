<?php

namespace Haida\FilamentCryptoGateway\Policies;

use App\Models\User;
use Filamat\IamSuite\Support\IamAuthorization;
use Haida\FilamentCryptoGateway\Models\CryptoInvoicePayment;

class CryptoInvoicePaymentPolicy
{
    public function viewAny(User $user): bool
    {
        return IamAuthorization::allowsAny([
            'crypto.invoice_payments.view',
            'crypto.invoice_payments.manage',
        ], null, $user);
    }

    public function view(User $user, CryptoInvoicePayment $record): bool
    {
        return IamAuthorization::allowsAny([
            'crypto.invoice_payments.view',
            'crypto.invoice_payments.manage',
        ], IamAuthorization::resolveTenantFromRecord($record), $user);
    }

    public function create(User $user): bool
    {
        return IamAuthorization::allows('crypto.invoice_payments.manage', null, $user);
    }

    public function update(User $user, CryptoInvoicePayment $record): bool
    {
        return IamAuthorization::allows('crypto.invoice_payments.manage', IamAuthorization::resolveTenantFromRecord($record), $user);
    }

    public function delete(User $user, CryptoInvoicePayment $record): bool
    {
        return IamAuthorization::allows('crypto.invoice_payments.manage', IamAuthorization::resolveTenantFromRecord($record), $user);
    }

    public function restore(User $user, CryptoInvoicePayment $record): bool
    {
        return IamAuthorization::allows('crypto.invoice_payments.manage', IamAuthorization::resolveTenantFromRecord($record), $user);
    }

    public function forceDelete(User $user, CryptoInvoicePayment $record): bool
    {
        return IamAuthorization::allows('crypto.invoice_payments.manage', IamAuthorization::resolveTenantFromRecord($record), $user);
    }
}
